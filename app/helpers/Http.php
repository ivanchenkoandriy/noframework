<?php

namespace app\helpers\Http {

    /**
     * Header for "Not found"
     */
    function notFound() {
        header("HTTP/1.0 404 Not Found");
    }

    /**
     * Header for redirect
     */
    function redirect($url, $permanent = false) {
        header('Location: ' . $url, true, $permanent ? 301 : 302);
    }

    /**
     * Is this AJAX?
     *
     * @return bool
     */
    function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 'xmlhttprequest' === strtolower($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

}

