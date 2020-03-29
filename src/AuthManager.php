<?php

namespace ByTIC\Auth;

/**
 * Class AuthManager
 * @package ByTIC\Auth
 */
class AuthManager
{
    use AuthManager\CanCreateUserProviders;
    use AuthManager\CanCreateGuardAuthenticators;

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