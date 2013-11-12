<?php
/**
 * @author      Alexander Stepanenko <alex.stepanenko@gmail.com>
 * @license     http://mit-license.org/
 */


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


}