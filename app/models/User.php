<?php

namespace app\models;

/**
 * Class for User
 *
 * @author Andriy Ivanchenko
 */
class User {

    /**
     * Request
     *
     * @var \Symfony\Component\HttpFoundation\Request
     */
    private $request;

    /**
     * Session
     *
     * @param \Symfony\Component\HttpFoundation\Session\SessionInterface $session
     */
    private $session;

    /**
     * Constructor
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function __construct(\Symfony\Component\HttpFoundation\Request $request) {
        $this->request = $request;
        $this->session = $request->getSession();
    }

    /**
     * Authorized
     *
     * @return bool
     */
    public function authorized(): bool {
        return true === $this->session->get('admin');
    }

    /**
     * Login
     *
     * @return bool
     */
    public function login(): bool {
        if (getenv('ADMIN_LOGIN') === $this->request->get('user') && getenv(ADMIN_PASSWORD) === $this->request->get('password')) {
            $this->session->set('admin', true);

            return true;
        }

        return false;
    }

    /**
     * Logout
     */
    public function logout() {
        $this->session->remove('admin');
    }

}
