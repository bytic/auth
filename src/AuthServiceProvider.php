<?php

namespace ByTIC\Auth;

use Nip\Container\ServiceProviders\Providers\AbstractSignatureServiceProvider;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

/**
 * Class AuthServiceProvider
 * @package ByTIC\Auth
 */
class AuthServiceProvider extends AbstractSignatureServiceProvider
{

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->registerManager();
        $this->registerUserProvider();
        $this->registerGuardHandler();
        $this->registerTokenStorage();
    }

    protected function registerManager()
    {
        $this->getContainer()->share(
            'auth',
            function () {
                return new AuthManager();
            }
        );
    }

    /**
     * Register a resolver for the authenticated user.
     *
     * @return void
     */
    protected function registerUserProvider()
    {
        $this->getContainer()->share(
            'auth.user_provider',
            function () {
                return $this->getContainer()->get('auth')->userProvider();
            }
        );
    }

    protected function registerGuardHandler()
    {
        $this->getContainer()->share(
            'auth.guard_handler',
            function () {
                return new GuardAuthenticatorHandler($this->getContainer()->get('auth.token_storage'));
            }
        );
    }

    protected function registerTokenStorage()
    {
        $this->getContainer()->share(
            'auth.token_storage',
            function () {
                return new TokenStorage();
            }
        );
    }

    /**
     * @inheritdoc
     */
    public function provides()
    {
        return [
            'auth',
            'auth.user_provider',
            'auth.token_storage',
            'auth.guard_handler',
        ];
    }
}