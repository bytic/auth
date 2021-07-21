<?php

namespace ByTIC\Auth\AuthManager;

use ByTIC\Auth\Security\Guard\GuardAuthenticatorInvoker;

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

    /**
     * @param $request
     * @return bool
     */
    public function authRequest($request)
    {
        return (new GuardAuthenticatorInvoker(null, $request))();
    }
}