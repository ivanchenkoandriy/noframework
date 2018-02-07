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
     * Header for "Not allowed"
     */
    function notAllowed() {
        header("HTTP/1.0 405 Method Not Allowed");
    }

}

