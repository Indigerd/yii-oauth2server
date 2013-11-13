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

class OAuth2ServerResource extends OAuth2ServerComponent {

    public
        $clientModel = 'OAuth2ServerModelClient',
        $sessionModel = 'OAuth2ServerModelSession',
        $scopeModel = 'OAuth2ServerModelScope';

    protected $request, $server;

    public function init() {
        $this->request = new \OAuth2\Util\Request();
        $this->server  = new \OAuth2\ResourceServer(new $this->sessionModel, new $this->scopeModel);
    }

    public function checkToken() {
        // Test for token existance and validity
        try {
            $this->server->isValid();
        }
        // The access token is missing or invalid...
        catch (\OAuth2\Exception\InvalidAccessTokenException $e) {
            $res = new OAuth2ServerResponse;
            $res->header('WWW-Authenticate', 'error="invalid_token", error_description="'.$e->getMessage().'"')
                ->set(array('error_msg' => $e->getMessage(), 'error_code' => $e->getCode()))
                ->send(401);
        }
        return true;
    }


}