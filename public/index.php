<?php

use DI\Container;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use function app\helpers\Http\notAllowed;
use function app\helpers\Http\notFound;
use function FastRoute\simpleDispatcher;

/* @var $container Container */
$container = require __DIR__ . '/../app/bootstrap.php';

$dispatcher = simpleDispatcher(function (RouteCollector $r) {
    $r->addRoute('GET', '/', ['app\controllers\MainController', 'index']);
    $r->addRoute('GET', '/add', ['app\controllers\MainController', 'add']);
    $r->addRoute('POST', '/add-handler', ['app\controllers\MainController', 'addHandler']);
    $r->addRoute('POST', '/preview', ['app\controllers\MainController', 'preview']);
    $r->addRoute('GET', '/view/{id:\d+}', ['app\controllers\MainController', 'view']);
    $r->addRoute('GET', '/edit/{id:\d+}', ['app\controllers\MainController', 'edit']);
    $r->addRoute('POST', '/edit-handler/{id:\d+}', ['app\controllers\MainController', 'editHandler']);
    $r->addRoute('GET', '/remove/{id:\d+}', ['app\controllers\MainController', 'remove']);
    $r->addRoute('POST', '/remove-handler/{id:\d+}', ['app\controllers\MainController', 'removeHandler']);
    $r->addRoute('POST', '/auth/login', ['app\controllers\AuthController', 'login']);
    $r->addRoute('GET', '/auth/logout', ['app\controllers\AuthController', 'logout']);
});

$requestMethod = filter_input(INPUT_SERVER, 'REQUEST_METHOD');
$requestUri = filter_input(INPUT_SERVER, 'REQUEST_URI');

// Strip query string (?foo=bar)
if (false !== $pos = strpos($requestUri, '?')) {
    $requestUri = substr($requestUri, 0, $pos);
}

// and decode URI
$requestUri = rawurldecode($requestUri);

$routeInfo = $dispatcher->dispatch($requestMethod, $requestUri);
switch ($routeInfo[0]) {
    case Dispatcher::NOT_FOUND:
        notFound();
        echo '404 Not Found';
        break;
    case Dispatcher::METHOD_NOT_ALLOWED:
        notAllowed();
        echo '405 Method Not Allowed';
        break;
    case Dispatcher::FOUND:
        $controller = $routeInfo[1];
        $parameters = $routeInfo[2];

        echo $container->call($controller, $parameters);
        break;
}