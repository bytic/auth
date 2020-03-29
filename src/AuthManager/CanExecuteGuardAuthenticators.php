<?php

namespace ByTIC\Auth\AuthManager;

use ByTIC\Auth\Security\Guard\GuardAuthenticatorInvoker;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Guard\Token\PreAuthenticationGuardToken;

/**
 * Trait CanExecuteGuardAuthenticators
 * @package ByTIC\Auth\AuthManager
 */
trait CanExecuteGuardAuthenticators
{
    /**
     * @param $authenticator
     * @param $request
     * @return bool
     */
    public function authRequestWith($authenticator, $request)
    {
        return (new GuardAuthenticatorInvoker($authenticator, $request))();
    }
}