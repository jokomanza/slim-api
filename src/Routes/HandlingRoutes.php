<?php

use Slim\App;

return function (App $app) {
    # $app->map(['GET'], '', \App\Controllers\StudentsController::class . ':get'); // if you need to match root of the group, you should use "map" instead of http method name
    # $app->get('/{id}', \App\Controllers\StudentsController::class . ':getItem');
    $app->get('/area', \App\Controllers\HandlingController::class . ':getArea')->setName('get semua area');
    # $app->map(['post'], '', \App\Controllers\StudentsController::class . ':post');
};