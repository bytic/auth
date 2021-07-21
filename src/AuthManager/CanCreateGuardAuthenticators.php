<?php

namespace ByTIC\Auth\AuthManager;

use ByTIC\Auth\Security\Guard\Authenticator\BaseAuthenticator;
use InvalidArgumentException;
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
    public function guardAuthenticator($authenticatorName = null)
    {
        $authenticatorName = $authenticatorName ?: $this->getDefaultGuardAuthenticator();

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

        $config = $this->config('authenticators.'.$authenticatorName)->toArray();
        if (is_null($config)) {
            throw new InvalidArgumentException("Auth guard [{$authenticatorName}] is not defined.");
        }

        $config = is_array($config) ? $config:  ['class' => $config];
        $authenticatorClass = (string) $config['class'];

        return app()->get($authenticatorClass);
    }

    /**
     * @return string
     */
    protected function getDefaultGuardAuthenticator()
    {
        return $this->config('defaults.authenticator', BaseAuthenticator::class);
    }
}
