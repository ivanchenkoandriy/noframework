<?php

namespace app\controllers;

use app\Response;

/**
 * Controller for authentication
 *
 * @author Andriy Ivanchenko
 */
class AuthController {

    /**
     * View
     *
     * @var Twig_Environment
     */
    private $view;

    /**
     * Constructor
     *
     * @param Twig_Environment $view
     */
    public function __construct(\Twig_Environment $view) {
        $this->view = $view;
    }

    /**
     * Login
     *
     * @return string Returns HTML
     */
    public function login(\app\models\User $user) {
        $user->login();
        if ($user->authorized()) {
            \app\helpers\Http\redirect('/');
            return;
        } else {
            return $this->view->render('tasks/access-denied.twig', [
                        'result' => Response::createFail('Access denied!')
            ]);
        }
    }

    /**
     * Logout
     *
     * @return string Returns HTML
     */
    public function logout(\app\models\User $user) {
        $user->logout();
        \app\helpers\Http\redirect('/');
        return;
    }

}
