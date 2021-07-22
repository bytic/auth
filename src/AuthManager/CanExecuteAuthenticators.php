<?php

namespace ByTIC\Auth\AuthManager;

use ByTIC\Auth\Security\Guard\GuardAuthenticatorInvoker;

/**
 * Trait CanExecuteAuthenticators
 * @package ByTIC\Auth\AuthManager
 */
trait CanExecuteAuthenticators
{
    /**
     * @param $authenticator
     * @param $request
     * @return bool
     */
    public function authRequestWith($authenticator, $request)
    {
        $authenticator = is_object($authenticator) ? $authenticator : $this->guardAuthenticator($authenticator);
        return (new GuardAuthenticatorInvoker($authenticator, $request))();
    }

    /**
     * @param $request
     * @return bool
     */
    public function authenticateRequest($request)
    {
        $authenticator = $this->guardAuthenticator();
        return (new GuardAuthenticatorInvoker($authenticator, $request))();
    }
}