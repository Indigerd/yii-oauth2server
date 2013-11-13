<?php
/**
 * @author      Alexander Stepanenko <alex.stepanenko@gmail.com>
 * @license     http://mit-license.org/
 */

class OAuth2ServerModelSession extends CActiveRecord implements \OAuth2\Storage\SessionInterface {

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{oauth2server_sessions}}';
    }

    public function createSession($clientId, $redirectUri, $type = 'user', $typeId = null, $authCode = null, $accessToken = null, $refreshToken = null, $accessTokenExpire = null, $stage = 'requested')
    {
        $now = time();
        $session = OAuth2ServerModelSession::model();

        $session->client_id = $clientId;
        $session->redirect_uri = $redirectUri;
        $session->owner_type = $type;
        $session->owner_id = $typeId;
        $session->auth_code = $authCode;
        $session->access_token = $accessToken;
        $session->refresh_token = $refreshToken;
        $session->access_token_expires = $accessTokenExpire;
        $session->stage = $stage;
        $session->first_requested = $now;
        $session->last_updated = $now;


        $session->setIsNewRecord(true);
        $session->save();
        return $session->id;
    }

    public function updateSession($sessionId, $authCode = null, $accessToken = null, $refreshToken = null, $accessTokenExpire = null, $stage = 'requested')
    {
        if ($session = $this->findByPk($sessionId)) {
            $session->auth_code = $authCode;
            $session->access_token = $accessToken;
            $session->refresh_token = $refreshToken;
            $session->access_token_expires = $accessTokenExpire;
            $session->stage = $stage;
            $session->last_updated = time();
            $session->save();
        }
    }

    public function deleteSession($clientId, $type, $typeId)
    {
        $bind = array(
          ':client_id'  => $clientId,
          ':owner_type' => $type,
          ':owner_id'   => $typeId,
        );
        if ($session = $this->find('client_id = :client_id and owner_type = :owner_type and owner_id = :owner_id', $bind)) {
            $session->delete();
        }
    }

    public function validateAuthCode($clientId, $redirectUri, $authCode)
    {
        $bind = array(
            ':client_id' => $clientId,
            ':redirect_uri' => $redirectUri,
            ':auth_code' => $authCode,
        );
        if ($session = $this->find('client_id = :client_id and redirect_uri = :redirect_uri and auth_code = :auth_code', $bind)) {
            return $session->getAttributes();//(array)$session;
        }
        return false;
    }

    public function validateAccessToken($accessToken)
    {
        if ($session = $this->find('access_token = :access_token', array(':access_token'=>$accessToken))) {
            if ($session->access_token_expires < time()) return false;
            $session->access_token_expires = time() + \OAuth2\AuthServer::getExpiresIn();
            $session->last_updated = time();
            $session->save();
            return array(
                'id' => $session->id,
                'owner_type' => $session->owner_type,
                'owner_id' => $session->owner_id
            );
        }
        return false;
    }

    public function getAccessToken($sessionId)
    {
        if ($session = $this->findByPk($sessionId)) {
            return $session->access_token;
        }
        return false;
    }

    public function validateRefreshToken($refreshToken, $clientId)
    {
        $bind = array(
            ':refresh_token' => $refreshToken,
            ':client_id' => $clientId
        );
        if ($session = $this->find('refresh_token = :refresh_token and client_id = :client_id', $bind)) {
            return $session->id;
        }
        return false;
    }

    public function updateRefreshToken($sessionId, $newAccessToken, $newRefreshToken, $accessTokenExpires)
    {
        if ($session = $this->findByPk($sessionId)) {
            $session->access_token = $newAccessToken;
            $session->refresh_token = $newRefreshToken;
            $session->access_token_expires = $accessTokenExpires;
            $session->last_updated = time();
            $session->save();
        }
    }

    public function associateScope($sessionId, $scopeId)
    {
        $scope = OAuth2ServerModelSessionScope::model();
        $scope->session_id = $sessionId;
        $scope->scope_id = $scopeId;
        $scope->setIsNewRecord(true);
        $scope->save();
    }

    /*
     * @TODO IMPLEMENT!!!
     */
    public function getScopes($sessionId)
    {
        $scopes = array();
        //$r = \Mapper_Oauth2Scope::getInstance()->getAggregateFromDbBySql('SELECT oauth2_scopes.id, oauth2_scopes.scope, oauth2_scopes.name, oauth2_scopes.description FROM oauth2_session_scopes INNER JOIN oauth2_scopes ON oauth2_session_scopes.scope_id = oauth2_scopes.id WHERE session_id = '.intval($sessionId));
        //foreach($r->getContent() as $row) $scopes[] = $row->scope;
        return $scopes;
    }


}