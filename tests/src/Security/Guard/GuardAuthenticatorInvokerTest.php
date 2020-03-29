<?php

namespace ByTIC\Auth\Tests\Security\Guard;

use ByTIC\Auth\AuthServiceProvider;
use ByTIC\Auth\Security\Guard\GuardAuthenticatorInvoker;
use ByTIC\Auth\Tests\AbstractTest;
use ByTIC\Auth\Tests\Fixtures\Security\Guard\AppCustomAuthenticator;
use Nip\Config\Config;
use Nip\Container\Container;
use Nip\Request;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

/**
 * Class GuardAuthenticatorInvokerTest
 * @package ByTIC\Auth\Tests\Security\Guard
 */
class GuardAuthenticatorInvokerTest extends AbstractTest
{
    public function test_return_false_on_not_supported_request()
    {
        $this->initServiceProvider();

        $request = new Request();

        $result = (new GuardAuthenticatorInvoker(AppCustomAuthenticator::class, $request))();
        self::assertFalse($result);
    }

    public function test_return_token()
    {
        $this->initServiceProvider();

        $request = new Request();
        $request->request->set('_username', 'john');
        $request->request->set('_password', '123456');

        $result = (new GuardAuthenticatorInvoker(AppCustomAuthenticator::class, $request))();
        self::assertInstanceOf(PostAuthenticationGuardToken::class, $result);
    }

    protected function initServiceProvider()
    {
        $container = Container::getInstance();
        $container->set('config', new Config());
//        $this->loadConfigIntoContainer('basic');
        $serviceProvider = new AuthServiceProvider();
        $serviceProvider->setContainer($container);
        $serviceProvider->register();
    }
}
