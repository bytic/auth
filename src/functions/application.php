<?php

if (!function_exists('auth')) {
    /**
     * Get the available auth manager.
     *
     * @return \ByTIC\Auth\Manager\AuthManager
     */
    function auth()
    {
        return app('auth');
    }
}
