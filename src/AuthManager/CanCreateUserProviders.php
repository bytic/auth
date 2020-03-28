<?php

namespace ByTIC\Auth\AuthManager;

use ByTIC\Auth\Security\Core\UserProvider\IdentifierUserProvider;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Trait CanCreateUserProviders
 * @package ByTIC\Auth\AuthManager
 */
trait CanCreateUserProviders
{
    /**
     * @var UserProviderInterface[]
     */
    protected $userProviders = [];

    /**
     * @param null $provider
     * @return UserProviderInterface
     */
    public function userProvider($provider = null)
    {
        $provider = $provider ?: $this->getDefaultUserProvider();

        if (!isset($this->userProviders[$provider])) {
            $userProvider = $this->createUserProvider($provider);
            $this->userProviders[$provider] = $userProvider ?: false;
        }

        return $this->userProviders[$provider];
    }

    /**
     * @param null $provider
     * @return UserProviderInterface|void
     */
    protected function createUserProvider($provider = null)
    {
        $provider = $provider ?: $this->getDefaultUserProvider();
        $config = $this->getUserProviderConfiguration($provider);
        if (is_null($config) && $provider != 'identifier') {
            return;
        }

        $driver = $config['driver'] ?? null;

//        if (isset($this->customProviderCreators[$driver = ($config['driver'] ?? null)])) {
//            return call_user_func(
//                $this->customProviderCreators[$driver], $this->app, $config
//            );
//        }

        switch ($driver) {
            case 'identifier':
            case IdentifierUserProvider::class:
            default:
                return $this->createIdentifierUserProvider();
        }
    }

    /**
     * @param $config
     * @return IdentifierUserProvider
     */
    protected function createIdentifierUserProvider($config = null)
    {
        return new IdentifierUserProvider();
    }

    /**
     * Get the user provider configuration.
     *
     * @param string|null $provider
     * @return array|null
     */
    protected function getUserProviderConfiguration($provider)
    {
        return ($provider) ? $this->config('auth.providers.'.$provider) : null;
    }

    /**
     * Get the default user provider name.
     *
     * @return string
     */
    public function getDefaultUserProvider()
    {
        return $this->config('auth.defaults.provider', 'identifier');
    }
}
