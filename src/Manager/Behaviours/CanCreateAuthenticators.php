<?php

namespace ByTIC\Auth\Manager\Behaviours;

use ByTIC\Auth\Security\Authenticator\BaseAuthenticator;
use InvalidArgumentException;
use Nip\Config\Config;
use Symfony\Component\Security\Http\Authentication\AuthenticatorManager;
use Symfony\Component\Security\Http\Authentication\AuthenticatorManagerInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;

/**
 * Trait CanCreateGuardAuthenticators
 * @package ByTIC\Auth\AuthManager
 */
trait CanCreateAuthenticators
{
    /**
     * @var AuthenticatorManagerInterface[]
     */
    protected $authenticators = [];

    /**
     * @param null $authenticatorName
     * @return AbstractAuthenticator
     * @noinspection PhpDocMissingThrowsInspection
     */
    public function authenticator($authenticatorName = null)
    {
        $authenticatorName = $authenticatorName ?: $this->getDefaultAuthenticator();

        if (empty($authenticatorName)) {
            /** @noinspection PhpUnhandledExceptionInspection */
            throw new \Exception("guardAuthenticator function need a name");
        }

        if (!isset($this->authenticators[$authenticatorName])) {
            $authenticator = $this->createAuthenticator($authenticatorName);

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
     * @return AuthenticatorManager
     */
    protected function createAuthenticator($authenticatorName)
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
    protected function getDefaultAuthenticator(): string
    {
        return $this->config('defaults.authenticator', BaseAuthenticator::class);
    }
}
