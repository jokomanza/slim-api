<?php

use Slim\App;

return function (App $app) {
    # $app->map(['GET'], '', \App\Controllers\StudentsController::class . ':get'); // if you need to match root of the group, you should use "map" instead of http method name
    # $app->get('/{id}', \App\Controllers\StudentsController::class . ':getItem');
    $app->get('/area', \App\Controllers\HandlingController::class . ':getArea')->setName('get semua area');
    $app->get('/film', \App\Controllers\HandlingController::class . ':getFilm')->setName('get semua data film');
    $app->get('/film/{film_id:[0-9]+}/actors', \App\Controllers\HandlingController::class . ':getActorsByFilm')->setName('actors_by_film');
    $app->get('/actor/{actor_id:[0-9]+}', \App\Controllers\HandlingController::class . ':getActorDetail')->setName('actor_detail');
    # $app->map(['post'], '', \App\Controllers\StudentsController::class . ':post');
};