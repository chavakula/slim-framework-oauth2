<?php

/*
 * Simple example to implement OAuth 2.0 in PHP Slim framework Ver 3.9
 * I am using "bshaffer" library @ https://github.com/bshaffer/oauth2-server-php
 * Say HI at email: ch.rajshekar@gmail.com, Skype: ch.rajshekar
 */

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require __DIR__ . '/vendor/autoload.php';

// configuration for Oauth2 DB
$config['displayErrorDetails'] = true;
$config['odb']['host'] = "127.0.0.1";
$config['odb']['user'] = "root";
$config['odb']['pass'] = "mysql";
$config['odb']['dbname'] = "oauth";

$app = new Slim\App(["settings" => $config]);

$container = $app->getContainer();


// Container to create a oauth2 database connection
$container['oauth'] = function($c){
    $db = $c['settings']['odb'];

    OAuth2\Autoloader::register();
    $storage = new OAuth2\Storage\Pdo(array('dsn' => "mysql:dbname=".$db['dbname'].";host=".$db['host'], 'username' => $db['user'], 'password' => $db['pass']));
    return $storage;
};

$app->get('/', function ($request, $response) {
    return 'Welcome to slim 3.9 framework tutorial on how to implement OAuth 2.0';
});

$app->post('/generateToken',function(Request $request, Response $response){

    // @ generate a fresh token
    // @ Token is valid till 1 hr or 3600 seconds after which it expires
    // @ Token will not be auto refreshed
    // @ generation of a new token should be handled at application level by calling this api

    // @ add parameter : ,['access_lifetime'=>3600] if you want to extent token life time from default 3600 seconds

    $server = new OAuth2\Server($this->oauth);
    $server->addGrantType(new OAuth2\GrantType\ClientCredentials($this->oauth));
    $server->addGrantType(new OAuth2\GrantType\AuthorizationCode($this->oauth));

    // @ generate a Oauth 2.0 token in json with format below
    // @ {"access_token":"ac7aeb0ee432bf9b73f78985c66a1ad878593530","expires_in":3600,"token_type":"Bearer","scope":null}
    $server->handleTokenRequest(OAuth2\Request::createFromGlobals())->send();

});

$app->get('/validateToken',function(Request $request, Response $response){

    // @ Validate Oauth Token passed via http headers in "Authorization bearer"
    $validate = new Tokens($this->oauth);
    $validate->validateToken();

    // @ Pass a Message if Oauth 2.0 token is valid to complete test
    return json_encode(array('success' => true, 'message' => 'Aaila! You have a valid Oauth2.0 Token'));

});


$app->run();
