<?php

namespace ByTIC\Auth;

use ByTIC\Auth\Services\JWTManager;

/**
 * Class AuthManager
 * @package ByTIC\Auth
 */
class AuthManager
{
    use AuthManager\CanCreateUserProviders;
    use AuthManager\CanCreateAuthenticators;
    use AuthManager\CanExecuteAuthenticators;

    /**
     * @return JWTManager
     */
    public function jwtManager(): JWTManager
    {
        return app(AuthServiceProvider::JWT_MANAGER);
    }

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