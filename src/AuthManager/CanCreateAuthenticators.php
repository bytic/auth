<?php

namespace ByTIC\Auth\AuthManager;

use ByTIC\Auth\Security\Guard\Authenticator\BaseAuthenticator;
use InvalidArgumentException;
use Nip\Config\Config;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Guard\Authenticator\GuardBridgeAuthenticator;

/**
 * Trait CanCreateGuardAuthenticators
 * @package ByTIC\Auth\AuthManager
 */
trait CanCreateAuthenticators
{
    /**
     * @var AbstractGuardAuthenticator[]
     */
    protected $authenticators = [];

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

        if (!isset($this->authenticators[$authenticatorName])) {
            $authenticator = $this->createGuardAuthenticator($authenticatorName);

//            if ($authenticator instanceof AbstractGuardAuthenticator) {
//                $authenticator = new GuardBridgeAuthenticator($authenticator, $this->userProvider());
//            }
            $this->authenticators[$authenticatorName] = $authenticator ?: false;
            if (is_object($authenticator)) {
                $this->authenticators[get_class($authenticator)] = $authenticator;
            }
        }

        return $this->authenticators[$authenticatorName];
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

        $config = $this->config('authenticators.'.$authenticatorName);
        $config = $config instanceof Config ? $config->toArray() : $config;
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
