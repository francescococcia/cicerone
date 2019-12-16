<?php

/**
 * Front controller
 *
 * PHP version 7.0
 */

/**
 * Composer
 */
require dirname(__DIR__) . '/vendor/autoload.php';


/**
 * Error and Exception handling
 */
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');



session_start();

/**
 * Routing
 */
$router = new Core\Router();

// Add the routes
$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('login', ['controller' => 'Login', 'action' => 'login']);
$router->add('registration', ['controller' => 'Signup', 'action' => 'registration']);
$router->add('logout', ['controller' => 'Login', 'action' => 'destroy']);
$router->add('excursion/find', ['controller' => 'Home', 'action' => 'findExcursionBy']);
$router->add('excursion/findby', ['controller' => 'Home', 'action' => 'resultSearchingExcursions']);
$router->add('excursion/add', ['controller' => 'Home', 'action' => 'addExcursion']);
$router->add('excursion/offered', ['controller' => 'RequestExcursion', 'action' => 'excursionsOffered']);
$router->add('excursion/booked', ['controller' => 'RequestExcursion', 'action' => 'excursionsBooked']);
$router->add('send/ticket', ['controller' => 'Reporting', 'action' => 'report']);

$router->add('login/forgot', ['controller' => 'Password', 'action' => 'forgot_password']);
$router->add('password/reset/{token:[\da-f]+}', ['controller' => 'Password', 'action' => 'reset']);
$router->add('registration/activate/{token:[\da-f]+}', ['controller' => 'Signup', 'action' => 'accountActivation']);
$router->add('userprofile/show/{public:[\d0-9]+}', ['controller' => 'Profile', 'action' => 'publicUser']);
$router->add('userprofile/show/{public:[\d0-9]+}/review', ['controller' => 'RatingFeedback', 'action' => 'showRating']);
$router->add('excursion/offered/globtrotters/{globtrotters:[\d0-9]+}', ['controller' => 'RequestExcursion', 'action' => 'showGlobtrotters']);
$router->add('dashboard', ['controller' => 'Profile', 'action' => 'dashboard']);
$router->add('{controller}/{action}');

$router->dispatch($_SERVER['QUERY_STRING']);
