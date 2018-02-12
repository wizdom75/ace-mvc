<?php
/**
 * Created by PhpStorm.
 * User: wizdom75
 * Date: 25/01/2018
 * Time: 22:27
 */

/**
 * Autoloader
 */
require_once dirname(__DIR__).'/vendor/autoload.php';

/**
 *  Error and Exception handling
 */
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

/**
 *  Session start
 */

session_start();

//initialize the router
$router = new Core\Router();



// Add the routes

$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('login', ['controller' => 'Login', 'action'=>'new']);
$router->add('logout', ['controller' => 'Login', 'action'=>'destroy']);
$router->add('signup', ['controller' => 'Signup', 'action'=>'new']);
$router->add('{controller}/{action}');
$router->add('{controller}/{id:\d+}/{action}');
$router->add('password/reset/{token:[\da-f]+}', ['controller'=>'Password', 'action'=>'reset']);
$router->add('signup/activate/{token:[\da-f]+}', ['controller'=>'Signup', 'action'=>'activate']);
$router->add('admin/{controller}/{action}', ['namespace' => 'Admin']);


$router->dispatch($_SERVER['QUERY_STRING']);

