<?php

namespace ByTIC\Auth\Tests;

use ByTIC\Auth\AuthManager;
use ByTIC\Auth\AuthServiceProvider;
use ByTIC\Auth\Security\Core\UserProvider\IdentifierUserProvider;
use Nip\Config\Config;
use Nip\Container\Container;

/**
 * Class AuthJWTServiceProviderTest
 * @package ByTIC\Auth\Tests
 */
class AuthServiceProviderTest extends AbstractTest
{
    public function test_registerManager()
    {
        $container = $this->initServiceProvider();

        $loader = $container->get('auth');
        self::assertInstanceOf(AuthManager::class, $loader);
    }

    public function test_registerUserProvider_noConfig()
    {
        $container = $this->initServiceProvider();
        $container->set('config', new Config());

        $loader = $container->get('auth.user_provider');
        self::assertInstanceOf(IdentifierUserProvider::class, $loader);
    }

    /**
     * @return Container
     */
    protected function initServiceProvider()
    {
        $container = Container::getInstance();
//        $this->loadConfigIntoContainer('basic');
        $serviceProvider = new AuthServiceProvider();
        $serviceProvider->setContainer($container);
        $serviceProvider->register();

        return $container;
    }
}
