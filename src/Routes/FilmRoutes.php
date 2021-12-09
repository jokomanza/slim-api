<?php

use Slim\App;

return function (App $app) {
    $app->get('/area', \App\Controllers\FilmController::class . ':getArea')->setName('get semua area');
    $app->get('/film', \App\Controllers\FilmController::class . ':getFilm')->setName('get semua data film');
    $app->get('/film/{film_id:[0-9]+}/actors', \App\Controllers\FilmController::class . ':getActorsByFilm')->setName('actors_by_film');
    $app->get('/actor/{actor_id:[0-9]+}', \App\Controllers\FilmController::class . ':getActorDetail')->setName('actor_detail');
};