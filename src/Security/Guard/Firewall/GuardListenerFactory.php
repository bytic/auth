<?php

namespace ByTIC\Auth\Security\Guard\Firewall;

use ByTIC\Auth\Security\Guard\Provider\AuthenticationProviderFactory;
use Symfony\Component\Security\Guard\AuthenticatorInterface;
use Symfony\Component\Security\Guard\Firewall\GuardAuthenticationListener;

/**
 * Class GuardListenerFactory
 * @package ByTIC\Auth\Security\Guard\Firewall
 */
class GuardListenerFactory
{
    /**
     * @param string $providerKey
     * @param iterable|AuthenticatorInterface[] $guardAuthenticators
     * @return GuardAuthenticationListener
     */
    public static function create($providerKey, $guardAuthenticators)
    {
        $guardHandler = app('auth.guard_handler');
        $authenticationManager = AuthenticationProviderFactory::create($providerKey, $guardAuthenticators);

        return new GuardAuthenticationListener(
            $guardHandler,
            $authenticationManager,
            $providerKey,
            $guardAuthenticators
        );
    }
}