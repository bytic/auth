<?php

namespace ByTIC\Auth\Tests;

use ByTIC\Auth\AuthServiceProvider;
use ByTIC\Auth\Manager\AuthManager;
use ByTIC\Auth\Security\Core\UserProvider\IdentifierUserProvider;
use Nip\Config\Config;
use Nip\Container\Utility\Container;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

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

    public function test_registerUserChecker()
    {
        $container = $this->initServiceProvider();

        $loader = $container->get('auth.user_checker');
        self::assertInstanceOf(UserCheckerInterface::class, $loader);
    }

    public function test_registerTokenStorage()
    {
        $container = $this->initServiceProvider();

        $storage = $container->get('auth.token_storage');
        self::assertInstanceOf(TokenStorage::class, $storage);
    }

    public function test_registerGuardHandler()
    {
        $container = $this->initServiceProvider();

        $handler = $container->get('auth.guard_handler');
        self::assertInstanceOf(GuardAuthenticatorHandler::class, $handler);
    }

    /**
     * @return false|\Nip\Container\Container|Container|\Psr\Container\ContainerInterface
     */
    protected function initServiceProvider()
    {
        $container = Container::container();
//        $this->loadConfigIntoContainer('basic');
        $serviceProvider = new AuthServiceProvider();
        $serviceProvider->setContainer($container);
        $serviceProvider->register();

        return $container;
    }
}
