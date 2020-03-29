<?php

namespace ByTIC\Auth\Security\Guard\Provider;

use Symfony\Component\Security\Guard\Provider\GuardAuthenticationProvider;

/**
 * Class AuthenticationProviderFactory
 * @package ByTIC\Auth\Security\Guard\Provider
 */
class AuthenticationProviderFactory
{
    /**
     * @param string $providerKey
     * @param array $guardAuthenticators
     * @param null $userChecker
     * @return GuardAuthenticationProvider
     */
    public static function create($providerKey = '', $guardAuthenticators = [], $userChecker = null)
    {
        $userProvider = app('auth')->userProvider();
        $userChecker = $userChecker ?: app('auth.user_checker');

        return new GuardAuthenticationProvider(
            $guardAuthenticators,
            $userProvider,
            $providerKey,
            $userChecker
        );
    }
}
