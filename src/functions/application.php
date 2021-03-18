<?php

if (!function_exists('auth')) {
    /**
     * Get the available auth manager.
     *
     * @return \ByTIC\Auth\AuthManager
     */
    function auth()
    {
        return app('auth');
    }
}
