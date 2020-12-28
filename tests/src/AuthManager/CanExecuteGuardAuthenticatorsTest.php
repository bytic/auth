<?php

namespace ByTIC\Auth\Tests\AuthManager;

use ByTIC\Auth\AuthManager;
use ByTIC\Auth\AuthServiceProvider;
use ByTIC\Auth\Tests\AbstractTest;
use ByTIC\Auth\Tests\Fixtures\Security\Guard\AppCustomAuthenticator;
use Mockery\Mock;
use Nip\Config\Config;
use Nip\Container\Utility\Container;
use Nip\Http\Request;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

/**
 * Class CanExecuteGuardAuthenticatorsTest
 * @package ByTIC\Auth\Tests\AuthManager
 */
class CanExecuteGuardAuthenticatorsTest extends AbstractTest
{

    public function test_authRequestWith_return_false_on_not_supported_request()
    {
        $container = Container::container();
        $container->set('config', new Config());
//        $this->loadConfigIntoContainer('basic');
        $serviceProvider = new AuthServiceProvider();
        $serviceProvider->setContainer($container);
        $serviceProvider->register();
        
        /** @var AuthManager|Mock $manager */
        $manager = \Mockery::mock(AuthManager::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $request = new Request();
        $result = $manager->authRequestWith(new AppCustomAuthenticator(), $request);
        self::assertFalse($result);
    }

    public function test_authRequestWith()
    {
        $container = Container::container();
        $container->set('config', new Config());
//        $this->loadConfigIntoContainer('basic');
        $serviceProvider = new AuthServiceProvider();
        $serviceProvider->setContainer($container);
        $serviceProvider->register();

        $request = new Request();
        $request->request->set('_username', 'john');
        $request->request->set('_password', '123456');

        /** @var AuthManager|Mock $manager */
        $manager = \Mockery::mock(AuthManager::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $result = $manager->authRequestWith(AppCustomAuthenticator::class, $request);
        self::assertInstanceOf(PostAuthenticationGuardToken::class, $result);
    }
}