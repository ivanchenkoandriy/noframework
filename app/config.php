<?php

use app\models\User;
use DI\Container;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Capsule\Manager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Twig\TwigFunction;
use function app\helpers\Html\pagination;
use function DI\factory;

// set database
$databaseFDH = factory(function() {
    $database = new Manager();
    $database->addConnection([
        'driver' => 'mysql',
        'host' => getenv('DATABASE_HOST'),
        'database' => getenv('DATABASE_DATABASE'),
        'username' => getenv('DATABASE_USERNAME'),
        'password' => getenv('DATABASE_PASSWORD'),
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix' => '',
    ]);

    $database->bootEloquent();
    $database->setAsGlobal();

    return $database;
});

// set twig
$viewDFH = function(Container $container) {
    // create twig
    $loader = new \Twig_Loader_Filesystem(VIEWS_PATH);
    $twig = new \Twig_Environment($loader);

    // add pagination
    $function = new TwigFunction('pagination', function(LengthAwarePaginator $paginator) {
        return pagination($paginator);
    });
    $twig->addFunction($function);

    // add authorized
    $request = $container->make(Request::class);
    $user = new \app\models\User($request);
    $twig->addGlobal('authorized', $user->authorized());

    return $twig;
};

// set request
$requestDFH = factory(function() {
    $session = new Session();
    /* if (!$session->isStarted()) {
      $session->start();
      } */

    $request = new Request($_GET, $_POST, [], $_COOKIE, $_FILES, $_SERVER);
    $request->setSession($session);

    return $request;
});
return [
    Request::class => $requestDFH,
    Manager::class => $databaseFDH,
    \Twig_Environment::class => $viewDFH,
];
