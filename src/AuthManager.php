<?php

namespace ByTIC\Auth;

use ByTIC\Auth\AuthManager\CanCreateUserProviders;

/**
 * Class AuthManager
 * @package ByTIC\Auth
 */
class AuthManager
{
    use CanCreateUserProviders;

    /**
     * Helper to get the config values.
     *
     * @param string $key
     * @param string $default
     *
     * @return mixed
     * @throws \Exception
     */
    protected function config($key, $default = null)
    {
        return config("auth.$key", $default);
    }
}