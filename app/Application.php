<?php

namespace app;

use app\controllers\BaseController;
use app\controllers\MainController;
use Hoa\Session\Session;
use Illuminate\Database\Capsule\Manager as Database;
use function app\helpers\Http\isAjax;
use function app\helpers\Http\notFound;

/**
 * Web Application
 *
 * @author Andriy Ivanchenko
 */
class Application {

    /**
     * Constructor
     *
     * @param \app\Router $router
     * @param array $controllerClasses
     * @param Database $database
     * @return type
     */
    public function __construct(Router $router, array $controllerClasses, Database $database) {
        // Start session
        Session::start();

        // Is the route found
        $isRouteMatch = $router->match();
        if (!$isRouteMatch) {
            notFound();
            echo 'Error: No route was found!';
            return;
        }

        // Set controller name
        $controllerName = $router->getControllerName();
        if (array_key_exists($controllerName, $controllerClasses)) {
            $controllerClass = $controllerClasses[$controllerName];
        } else {
            $controllerClass = MainController::class;
        }

        // Set action name
        $action = $router->getActionName();
        if ('' === $action) {
            $action = 'index';
        }

        /* @var $controller BaseController */
        $controller = new $controllerClass();
        if (method_exists($controller, $action)) {
            $controller->setDatabase($database);
            $controller->setView(new View());

            // Call the action
            $content = call_user_func_array([$controller, $action], $router->getParams());
            $controller->addLayoutParam('content', $content);
            if (isAjax()) {
                echo $content;
            } else {
                echo (new View())->render($controller->getLayout(), $controller->getLayoutParams());
            }
        } else {
            // The method does not exists
            notFound();
            echo 'Error: The method does not exist!';
        }

        return;
    }

}
