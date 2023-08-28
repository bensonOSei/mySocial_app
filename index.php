<?php

use Benson\InforSharing\Http\Router;

require_once 'vendor/autoload.php';

$router = new Router();
$router->get('/',function(){
    echo json_encode([
        'message' => 'Welcome to the API'
    ]);
});

// User routes
$router->get('/user/{id}', 'Benson\InforSharing\Controllers\UserController@show');
$router->post('/user', 'Benson\InforSharing\Controllers\UserController@create');
$router->post('/user/login', 'Benson\InforSharing\Controllers\AuthController@login');
$router->put('/user/{id}', 'Benson\InforSharing\Controllers\UserController@update');
$router->patch('/user/{id}', 'Benson\InforSharing\Controllers\UserController@update');
$router->delete('/user/{id}','Benson\InforSharing\Controllers\UserController@delete');
