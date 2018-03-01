<?php
/**
 * Created by PhpStorm.
 * User: rajshekar
 * Date: 23/02/18
 * Time: 16:09
 */

class Tokens{

    protected $storage;

    public function __construct($storage){
        $this->storage = $storage;
    }


    public function validateToken(){
        // Pass a storage object or array of storage objects to the OAuth2 server class
        $server = new OAuth2\Server($this->storage);

        // Add the "Client Credentials" grant type (it is the simplest of the grant types)
        $server->addGrantType(new OAuth2\GrantType\ClientCredentials($this->storage));

        // Add the "Authorization Code" grant type (this is where the oauth magic happens)
        $server->addGrantType(new OAuth2\GrantType\AuthorizationCode($this->storage));

        // Handle a request to a resource and authenticate the access token
        if (!$server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
            $server->getResponse()->send();
            die;
        }
    }

}