<?php

namespace app;

/**
 * Authorization class
 *
 * @author Andriy Ivanchenko
 */
class Auth {

    /**
     * Login
     * @return boolean
     */
    public static function login() {
        $user = filter_input(INPUT_POST, 'user', FILTER_SANITIZE_STRING, [
            'options' => [
                'default' => ''
            ]
        ]);

        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING, [
            'options' => [
                'default' => ''
            ]
        ]);

        if (ADMIN_LOGIN === $user && ADMIN_PASSWORD === $password) {
            $session = new \Hoa\Session\Session();
            $session['admin'] = true;

            return true;
        }

        return false;
    }

    /**
     * Authorized
     *
     * @return boolean
     */
    public static function autorized() {
        $session = new \Hoa\Session\Session();
        if (!$session->isEmpty()) {
            return true === $session['admin'];
        } else {
            return false;
        }
    }

    /**
     * Logout
     */
    public static function logout() {
        $session = new \Hoa\Session\Session();
        unset($session['admin']);
    }

}
