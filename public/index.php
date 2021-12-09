<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\StatusCode;
use Illuminate\Database\Capsule\Manager as Capsule;

require __DIR__ . '/../vendor/autoload.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('SLIM_START', microtime(true));

// error_reporting(E_ALL);
// ini_set('display_errors', 1);
// set_error_handler(function ($severity, $message, $file, $line) {
//     if (error_reporting() & $severity) {
//         echo "hi";
//         throw new \ErrorException($message, 0, $severity, $file, $line);
//     }
// });

$settings = require __DIR__ . '/../settings.php';

$app = new \Slim\App($settings);


// Register routes
require __DIR__ . '/../src/routes.php';

// Register utils
require __DIR__ . '/../src/Utils/Util.php';

// GET DI Container
$container = $app->getContainer();

$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO('pgsql:host=' . $db['host'] . ';dbname=' . $db['database'],
        $db['username'], $db['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

// Start DB connection
$container->get('db');

$capsule = new Capsule();

$capsule->addConnection($container->get('settings')['db']);

// Make this Capsule instance available globally via static methods... (optional)
$capsule->setAsGlobal();

// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
$capsule->bootEloquent();
$container['db2'] = $capsule;

$app->run();