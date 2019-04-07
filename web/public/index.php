<?php
/**
 * Copyright (c) 2017 Cyber Security & Privacy Foundation Pte. Ltd.- All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Cyber Security & Privacy Foundation Pte. Ltd.
 */

/*
 * Front Controller
 */

use App\Auth;

/**
 * PHP Composer autoload
 */
require '../vendor/autoload.php';

/**
 * Error & Exception Handlers
 */
if(\App\Config::DEBUG_MODE){
    error_reporting(E_ALL);
}
else{
    error_reporting(0);
}
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

/**
 * Sessions
 */
\Core\AppSession::start('/');
\Core\Utils::setSecurityHeaders();

//Routing
$router = new \Core\Router();
//Add the Routes
/**
 * Note: Don't change the order of routing table (otherwise, fixed paths will return 404 not error
 */
//static paths:
$router->add('', ['controller' => 'home', 'action' => 'index']);  // Home path

//Only controller specified, set action to -> index; example: /admin/ => Admin->index()
$router->add('{controller:[a-z-]+}/', [ 'action' => 'index']);

//Other Routes:
$router->add('{namespace}/{controller}/{action}');

$router->add('{controller}/{id:\d+}/{action}');
$router->add('{namespace}/{controller}/{id:\d+}/{action}');
$router->add('{namespace}/{id:\d+}/{controller}/{action}');

//Wild card controller, matches /controller/action pattern
$router->add('{controller}/{action}');

//Match the requested route
$url = $_SERVER['QUERY_STRING'];

$router->dispatch($url);

?>