<?php

use JSend\JSendResponse;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\StatusCode;

$handlingRoutes = require_once __DIR__ . '/Routes/HandlingRoutes.php';

$container = $app->getContainer();

$app->get('/', function(Request $req, Response $res) {
    # throw new RuntimeException('example error');
    return $res->withJson(JSendResponse::success('Welcome to slim api'));
});

// Auth group
$app->group('/handling', $handlingRoutes);