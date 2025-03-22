<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// $routes->get('/', 'Home::index');
// // Auth routes
// $routes->get('register', 'AuthController::showRegistrationForm');
// $routes->post('register', 'AuthController::register');

// $routes->get('login', 'AuthController::showLoginForm');
// $routes->post('login', 'AuthController::login');

// $routes->get('logout', 'AuthController::logout');
// $routes->get('dashboard', 'Home::index', ['filters' => 'auth']);
// class RouteConfig
// {
//     public $filters = [
//         'auth' => ['before' => ['dashboard', 'dashboard/*']]
//     ];
// }
// $routes->group('', ['filter' => 'auth'], function ($routes) {
//     $routes->get('/', 'Home::index');;
//     $routes->get('dashboard', 'Home::index');
// });
// $routes->get('/dashboard', 'ClassController::dashboard', ['filter' => 'auth']);

// $routes->group('class', ['filter' => 'auth'], function ($routes) {
//     // Teacher-only routes
//     $routes->get('create', 'ClassController::createForm', ['filter' => 'auth:teacher']);
//     $routes->post('create', 'ClassController::create', ['filter' => 'auth:teacher']);

//     // Student-only routes
//     $routes->get('join', 'ClassController::joinForm', ['filter' => 'auth:student']);
//     $routes->post('join', 'ClassController::join', ['filter' => 'auth:student']);

//     // Common routes for both roles
//     $routes->get('details/(:num)', 'ClassController::details/$1');
// });

// $routes->get('login', 'AuthController::showLoginForm');
// $routes->post('login', 'AuthController::login');
// $routes->get('register', 'AuthController::showRegistrationForm');
// $routes->post('register', 'AuthController::register');
// $routes->get('logout', 'AuthController::logout');

$routes->group('', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'ClassController::dashboard');
    $routes->get('dashboard', 'ClassController::dashboard');
});

$routes->group('class', ['filter' => 'auth'], function ($routes) {
    // Teacher-only routes
    $routes->get('create', 'ClassController::createForm', ['filter' => 'auth:teacher']);
    $routes->post('create', 'ClassController::create', ['filter' => 'auth:teacher']);

    // Routes for all users
    $routes->get('join', 'ClassController::joinForm');
    $routes->post('join', 'ClassController::join');
    $routes->get('details/(:num)', 'ClassController::details/$1');
});

$routes->get('login', 'AuthController::showLoginForm');
$routes->post('login', 'AuthController::login');
$routes->get('register', 'AuthController::showRegistrationForm');
$routes->post('register', 'AuthController::register');
$routes->get('logout', 'AuthController::logout');

// Asset routes
$routes->get('assets/(:any)', static function ($file) {
    $path = FCPATH . 'assets/' . $file;
    if (file_exists($path)) {
        $mime = mime_content_type($path);
        header('Content-Type: ' . $mime);
        readfile($path);
        exit;
    }
    throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
});
