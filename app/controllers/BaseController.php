<?php

namespace app\controllers;

use app\View;
use Illuminate\Database\Capsule\Manager;

/**
 * Base controller
 *
 * @author Andriy Ivanchenko
 */
abstract class BaseController {

    /**
     * View
     *
     * @var \app\View
     */
    protected $view;

    /**
     * Layouts parameters
     *
     * @var array
     */
    private $_layoutParams = [];

    /**
     * Database manager
     *
     * @var \Illuminate\Database\Capsule\Manager
     */
    protected $databaseManager;

    /**
     * Set view
     *
     * @param View $view
     */
    public function setView(View $view) {
        $this->view = $view;
    }

    /**
     * Set database manager
     *
     * @param Manager $databaseManager
     */
    public function setDatabase(Manager $databaseManager) {
        $this->databaseManager = $databaseManager;
    }

    /**
     * Add layouts parameter
     *
     * @param string $name Name
     * @param mix $value Value
     * @throws Exception
     */
    public function addLayoutParam(string $name, $value) {
        if (!array_key_exists($name, $this->_layoutParams)) {
            $this->_layoutParams[$name] = $value;
        } else {
            throw new Exception('The parameter alredy exist!');
        }
    }

    /**
     * Get layouts parameters
     *
     * @return array
     */
    public function getLayoutParams() {
        return $this->_layoutParams;
    }

    /**
     * Get layout
     *
     * return string
     */
    public function getLayout() {
        return 'layout.php';
    }

}
