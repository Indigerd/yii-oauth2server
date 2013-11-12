<?php
/**
 * @author      Alexander Stepanenko <alex.stepanenko@gmail.com>
 * @license     http://mit-license.org/
 */

class OAuth2ServerModelClient extends CActiveRecord implements \OAuth2\Storage\ClientInterface {

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{oauth2server_clients}}';
    }

    public function getClient($clientId = null, $clientSecret = null, $redirectUri = null)
    {
        $clientDetails = false;
        $searchParams = array('id' => $clientId);
        if ($clientSecret !== null) $searchParams['secret'] = $clientSecret;
        if ($client = $this->find($searchParams)) {
            $clientDetails = array(
                'client_id' => $clientId,
                'client secret' => $clientSecret,
                'redirect_uri' => $redirectUri,
                'name' => $client->name,
                'auto_approve' => $client->auto_approve
            );
        }
        return $clientDetails;
    }


}