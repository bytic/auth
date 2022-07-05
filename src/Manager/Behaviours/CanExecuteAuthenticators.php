<?php

namespace ByTIC\Auth\Manager\Behaviours;

/**
 * Trait CanExecuteAuthenticators
 * @package ByTIC\Auth\AuthManager
 */
trait CanExecuteAuthenticators
{
    /**
     * @param $authenticator
     * @param $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function authRequestWith($authenticator, $request)
    {
        $authenticator = is_object($authenticator) ? $authenticator : $this->authenticator($authenticator);
        $request->attributes->set('_security_authenticators', [$authenticator]);
        return $this->manager()->authenticateRequest($request);
    }

    /**
     * @param $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function authenticateRequest($request)
    {
        $authenticator = $this->authenticator();
        return $this->authRequestWith($authenticator, $request);
    }
}