<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
// Auth routes
$routes->get('register', 'AuthController::showRegistrationForm');
$routes->post('register', 'AuthController::register');

$routes->get('login', 'AuthController::showLoginForm');
