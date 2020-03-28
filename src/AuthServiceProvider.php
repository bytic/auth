<?php

namespace ByTIC\Auth;

use Nip\Container\ServiceProviders\Providers\AbstractSignatureServiceProvider;

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

    /**
     * @inheritdoc
     */
    public function provides()
    {
        return [
            'auth',
            'auth.user_provider',
        ];
    }
}