<?php

namespace app\controllers;

use app\Auth;
use function app\helpers\Http\redirect;

/**
 * Controller for authentication
 *
 * @author Andriy Ivanchenko
 */
class AuthController extends BaseController {

    /**
     * Login
     * 
     * @return string Returns HTML
     */
    public function login() {
        Auth::login();
        if (Auth::autorized()) {
            redirect('/');
            return;
        } else {
            return $this->view->render('tasks/access-denied.php');
        }
    }

    /**
     * Logout
     *
     * @return string Returns HTML
     */
    public function logout() {
        Auth::logout();
        redirect('/');
        return;
    }

}
