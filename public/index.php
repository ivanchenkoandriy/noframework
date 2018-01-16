<?php

use app\Application;
use app\controllers\AuthController;
use app\controllers\MainController;
use app\Router;
use Illuminate\Database\Capsule\Manager as Database;

require __DIR__ . '/../vendor/autoload.php';

/**
 * Root directory
 */
define('ROOT_PATH', realpath(__DIR__ . DIRECTORY_SEPARATOR . '..') . DIRECTORY_SEPARATOR);

/**
 * Application directory
 */
define('APP_PATH', ROOT_PATH . 'app' . DIRECTORY_SEPARATOR);

/**
 * Views path
 */
define('VIEWS_PATH', APP_PATH . 'views' . DIRECTORY_SEPARATOR);

/**
 * Autorization data
 */
define('ADMIN_LOGIN', 'admin');
define('ADMIN_PASSWORD', '123');

/**
 * Limit picture size
 */
define('PICTURE_WIDTH', 320);
define('PICTURE_HEIGHT', 240);

/**
 * Constants
 */
define('NO_FILE_WAS_UPLOADED', 4);
define('APP_CHARSET', 'utf-8');

/**
 * Regular expressions of paths
 */
$routes = [
    '/^\/$/',
    '/^\/(?<action>(add|preview))$/',
    '/^\/(?<action>(edit|remove|view))\/(?<id>[0-9]+)$/',
    '/^\/(?<controller>task)\/(?<action>add)$/',
    '/^\/(?<controller>auth)\/(?<action>(login|logout))$/'
];

/**
 * Existing controllers
 */
$controllerClasses = [
    '' => MainController::class,
    'auth' => AuthController::class
];

/**
 * Connect to a database
 */
$database = new Database();
$database->addConnection([
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'local_beegee',
    'username' => 'root',
    'password' => '7777777',
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
]);
$database->setAsGlobal();
$database->bootEloquent();

// create router
$router = new Router($routes);

// Execute application
new Application($router, $controllerClasses, $database);
