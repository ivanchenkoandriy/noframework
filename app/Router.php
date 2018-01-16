<?php

namespace app;

/**
 * Something like a router
 *
 * @author Andriy Ivanchenko
 */
class Router {

    /**
     * Routes
     *
     * @var array
     */
    private $routes = [];

    /**
     * Name of the controller
     *
     * @var string
     */
    private $controller = '';

    /**
     * Name of the action
     *
     * @var string
     */
    private $action = '';

    /**
     * Parameters
     *
     * @var array
     */
    private $params = [];

    /**
     * Constructor
     *
     * @param array $routes
     */
    public function __construct(array $routes) {
        $this->routes = $routes;
    }

    /**
     * To match a path
     *
     * @return bool
     */
    public function match(): bool {
        $requestUri = filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL);
        $url = parse_url($requestUri);
        $path = $url['path'];

        $matches = null;
        foreach ($this->routes as $pattern) {
            $isMatchFound = preg_match($pattern, $path, $matches);
            if (!$isMatchFound) {
                continue;
            }

            if (array_key_exists('controller', $matches)) {
                $this->controller = $matches['controller'];
            }

            if (array_key_exists('action', $matches)) {
                $this->action = $matches['action'];
            }

            if (array_key_exists('id', $matches)) {
                $this->params['id'] = $matches['id'];
            }

            return true;
        }

        return false;
    }

    /**
     * Get controller name
     *
     * @return string
     */
    public function getControllerName(): string {
        return $this->controller;
    }

    /**
     * Get action name
     *
     * @return string
     */
    public function getActionName(): string {
        return $this->action;
    }

    /**
     * Get parameters
     *
     * @return array
     */
    public function getParams(): array {
        return $this->params;
    }

}
