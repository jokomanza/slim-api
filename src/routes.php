<?php

use alsvanzelf\jsonapi\extensions\AtomicOperationsDocument;
use alsvanzelf\jsonapi\MetaDocument;
use alsvanzelf\jsonapi\ResourceDocument;
use JSend\JSendResponse;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\StatusCode;

$handlingRoutes = require_once __DIR__ . '/Routes/HandlingRoutes.php';

$container = $app->getContainer();

$app->get('/', \App\Controllers\MainController::class);

// Auth group
$app->group('/handling', $handlingRoutes);
