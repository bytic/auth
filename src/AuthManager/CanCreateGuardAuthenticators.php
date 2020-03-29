<?php

namespace ByTIC\Auth\AuthManager;

use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

/**
 * Trait CanCreateGuardAuthenticators
 * @package ByTIC\Auth\AuthManager
 */
trait CanCreateGuardAuthenticators
{
    /**
     * @var AbstractGuardAuthenticator[]
     */
    protected $guardAuthenticators = [];

    /**
     * @param null $authenticatorName
     * @return AbstractGuardAuthenticator
     * @noinspection PhpDocMissingThrowsInspection
     */
    public function guardAuthenticator($authenticatorName)
    {
        if (empty($authenticatorName)) {
            /** @noinspection PhpUnhandledExceptionInspection */
            throw new \Exception("guardAuthenticator function need a name");
        }

        if (!isset($this->guardAuthenticators[$authenticatorName])) {
            $authenticator = $this->createGuardAuthenticator($authenticatorName);
            $this->guardAuthenticators[$authenticatorName] = $authenticator ?: false;
            if (is_object($authenticator)) {
                $this->guardAuthenticators[get_class($authenticator)] = $authenticator;
            }
        }

        return $this->guardAuthenticators[$authenticatorName];
    }

    /**
     * @param $authenticatorName
     * @return AbstractGuardAuthenticator
     */
    protected function createGuardAuthenticator($authenticatorName)
    {
        if (class_exists($authenticatorName)) {
            return app($authenticatorName);
        }

        $key = 'auth.authenticators.'.$authenticatorName;
        if (app()->has($key)) {
            return app()->get($key);
        }

        return app()->get($authenticatorName);
    }
}
