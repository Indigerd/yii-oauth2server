<?php
/**
 * @author      Alexander Stepanenko <alex.stepanenko@gmail.com>
 * @license     http://mit-license.org/
 */

require_once 'OAuth2ServerComponent.php';
require_once 'OAuth2ServerModelClient.php';
require_once 'OAuth2ServerModelScope.php';
require_once 'OAuth2ServerModelSession.php';
require_once 'OAuth2ServerModelSessionScope.php';
require_once 'OAuth2ServerResponse.php';

class OAuth2ServerAuth extends OAuth2ServerComponent {

    public $grantTypesEnabled = array('AuthCode', 'RefreshToken', 'Implicit', 'Password');
    public $identityClass = null;
    public
        $clientModel = 'OAuth2ServerModelClient',
        $sessionModel = 'OAuth2ServerModelSession',
        $scopeModel = 'OAuth2ServerModelScope';
    //your application url where signed in user will accept or reject oauth2 application authorize
    public $authorizeEndpoint;
    //your application login url
    public $loginEndpoint;

    protected $request, $server;

    public function init() {
        $this->request = new \OAuth2\Util\Request();
        $this->server  = new \OAuth2\AuthServer(new $this->clientModel, new $this->sessionModel, new $this->scopeModel);
        foreach ($this->grantTypesEnabled as $grantType) {
            $methodName = 'enable'.$grantType;
            if (method_exists($this, $methodName)) call_user_func(array($this, $methodName));
        }
    }

    protected function enableAuthCode() {
        $this->server->addGrantType(new \OAuth2\Grant\AuthCode());
    }

    protected function enableRefreshToken() {
        $this->server->addGrantType(new \OAuth2\Grant\RefreshToken());
    }

    protected function enableImplicit() {
        $this->server->addGrantType(new \OAuth2\Grant\Implicit());
    }

    protected function enablePassword() {
        if ($identityClass = $this->identityClass) {
            $verifyCredentialsCallback = function ($login, $password) use ($identityClass) {
                $userId = false;
                try {
                    $identity = new $identityClass($login, $password);
                    if($identity->authenticate()) {
                        \Yii::app()->user->login($identity);
                        $userId = $identity->getId();
                    }
                } catch (\Exception $e) {
                    $userId = false;
                }
                return $userId;
            };
            $passwordGrant = new \OAuth2\Grant\Password();
            $passwordGrant->setVerifyCredentialsCallback($verifyCredentialsCallback);
            $this->server->addGrantType($passwordGrant);
        }
    }

    public function getSessionParams() {
        $params = \Yii::app()->session->get('OAuth2ServerParams', null);
        if ($params === null) {
            throw new \Exception('Missing auth parameters');
        }
        return unserialize($params);
    }

    public function isAutoApprove(){
        return
            (!\Yii::app()->user->getIsGuest() AND
                $params = $this->getSessionParams() AND
                isset($params['client_details']['auto_approve']) AND
                $params['client_details']['auto_approve']);
    }

    protected function checkAuth() {
        if (\Yii::app()->user->getIsGuest()) {
            \Yii::app()->user->setReturnUrl($_SERVER['REQUEST_URI']);
            \Yii::app()->getRequest()->redirect($this->loginEndpoint);
        }
    }

    public function initOAuth2Request() {
        try {
            $params = $this->server->checkAuthoriseParams();
            \Yii::app()->session['OAuth2ServerParams'] = serialize($params);
            // if authenticated redirect to authorize
            if (!\Yii::app()->user->getIsGuest())
                \Yii::app()->getRequest()->redirect($this->authorizeEndpoint);
            // else redirect the user to sign-in
            else {
                \Yii::app()->user->setReturnUrl($this->authorizeEndpoint);
                \Yii::app()->getRequest()->redirect($this->loginEndpoint);
            }
        } catch (\Exception $e) {
            $res = new OAuth2ServerResponse;
            $res->set(array('error_description' => $e->getMessage(), 'error' => $this->server->getExceptionType($e->getCode())))->send(400);
        }

    }

    public function authorize() {
        $params = $this->getSessionParams();
        $this->checkAuth();
        // If the user approves the client then generate an authoriztion code
        if ($this->isAutoApprove() OR isset($_POST['accept'])) {
            $authCode = $this->server->newAuthoriseRequest('user', \Yii::app()->user->getId(), $params);
            $urlParams = array(
                'code' => $authCode,
                'state'	=> $params['state']
            );
            //check if we need to deal with implicit grant
            if ($params['response_type'] == 'token') {
                try {
                    $params['grant_type'] = 'token';
                    $params['code'] = $authCode;
                    $urlParams = $this->server->issueAccessToken($params);
                } catch (\Exception $e) {
                    $res = new OAuth2ServerResponse;
                    $res->set(array('error_description' => $e->getMessage(), 'error' => $this->server->getExceptionType($e->getCode())))->send(400);
                }
            }
            // Generate the redirect URI
            \Yii::app()->getRequest()->redirect(\OAuth2\Util\RedirectUri::make($params['redirect_uri'], $urlParams));
        }
        // The user denied the request so send them back to the client with an error
        elseif (isset($_POST['reject'])) {
            $server = $this->server;
            \Yii::app()->getRequest()->redirect(\OAuth2\Util\RedirectUri::make($params['redirect_uri'], array(
                'error_msg' => 'access_denied',
                'error_message' => $server::getExceptionMessage('access_denied'),
                'state'	=> $params['state']
            )));
        }
    }

    public function issueAccessToken() {
        try {
            $res = new OAuth2ServerResponse;
            $res->set($this->server->issueAccessToken($_POST))->send();
        } catch (\Exception $e) {
            // Show an error message
            $res = new OAuth2ServerResponse;
            $res->set(array('error_description' => $e->getMessage(), 'error' => $this->server->getExceptionType($e->getCode())))->send(400);
        }
    }

}