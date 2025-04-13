<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */


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
    $routes->get('edit/(:num)', 'ClassController::editForm/$1', ['filter' => 'auth:teacher']);
    $routes->post('update/(:num)', 'ClassController::update/$1', ['filter' => 'auth:teacher']);
    $routes->get('delete/(:num)', 'ClassController::delete/$1', ['filter' => 'auth:teacher']);

    // Topic routes
    $routes->group('(:num)/topics', ['filter' => 'auth:teacher'], function ($routes) {
        $routes->get('', 'TopicController::getClassTopics/$1');
        $routes->post('', 'TopicController::create');
        $routes->put('(:num)', 'TopicController::update/$2');
        $routes->delete('(:num)', 'TopicController::delete/$2');
    });

    // Assignment routes
    $routes->get('(:num)/assignments', 'AssignmentController::listAssignments/$1');
    $routes->get('(:num)/assignments/create', 'AssignmentController::createForm/$1', ['filter' => 'auth:teacher']);
    $routes->post('(:num)/assignments/create', 'AssignmentController::create/$1', ['filter' => 'auth:teacher']);
    $routes->get('(:num)/assignments/(:num)', 'AssignmentController::details/$1/$2');
    $routes->get('(:num)/assignments/(:num)/edit', 'AssignmentController::editForm/$1/$2', ['filter' => 'auth:teacher']);
    $routes->post('(:num)/assignments/(:num)/update', 'AssignmentController::update/$1/$2', ['filter' => 'auth:teacher']);
    $routes->get('(:num)/assignments/(:num)/delete', 'AssignmentController::delete/$1/$2', ['filter' => 'auth:teacher']);
    $routes->post('(:num)/assignments/(:num)/submit', 'AssignmentController::submit/$1/$2', ['filter' => 'auth:student']);
    $routes->post('(:num)/assignments/submission/(:num)/grade', 'AssignmentController::gradeSubmission/$1/$2', ['filter' => 'auth:teacher']);

    // Assignment file routes
    $routes->get('(:num)/assignments/(:num)/download', 'AssignmentController::downloadFile/$1/$2');
    $routes->get('(:num)/assignments/(:num)/preview', 'AssignmentController::previewFile/$1/$2');
    $routes->get('(:num)/assignments/(:num)/submissions/(:num)/download', 'AssignmentController::downloadSubmissionFile/$1/$2/$3');
    $routes->get('(:num)/assignments/(:num)/submissions/(:num)/preview', 'AssignmentController::previewSubmissionFile/$1/$2/$3');

    // Assignment topic routes
    $routes->group('(:num)/assignments/(:num)/topics', ['filter' => 'auth:teacher'], function ($routes) {
        $routes->get('', 'TopicController::getAssignmentTopics/$2');
        $routes->post('', 'TopicController::assignToAssignment/$2');
    });
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
