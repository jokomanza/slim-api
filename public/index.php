<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\StatusCode;

require __DIR__ . '/../vendor/autoload.php';

// error_reporting(E_ALL);
// ini_set('display_errors', 1);
// set_error_handler(function ($severity, $message, $file, $line) {
//     if (error_reporting() & $severity) {
//         echo "hi";
//         throw new \ErrorException($message, 0, $severity, $file, $line);
//     }
// });


$config = [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header
    ],
];

$app = new \Slim\App($config);


// Register routes
require __DIR__ . '/../src/routes.php';

$app->run();