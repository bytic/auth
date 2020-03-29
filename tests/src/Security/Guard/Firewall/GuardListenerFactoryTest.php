<?php

namespace ByTIC\Auth\Tests\Security\Guard\Firewall;

use ByTIC\Auth\AuthServiceProvider;
use ByTIC\Auth\Security\Guard\Firewall\GuardListenerFactory;
use ByTIC\Auth\Tests\AbstractTest;
use Nip\Config\Config;
use Nip\Container\Container;
use Symfony\Component\Security\Guard\Firewall\GuardAuthenticationListener;

/**
 * Class GuardListenerFactoryTest
 * @package ByTIC\Auth\Tests\Security\Guard\Firewall
 */
class GuardListenerFactoryTest extends AbstractTest
{
    public function test_create()
    {
        $container = Container::getInstance();
        $container->set('config', new Config());
//        $this->loadConfigIntoContainer('basic');
        $serviceProvider = new AuthServiceProvider();
        $serviceProvider->setContainer($container);
        $serviceProvider->register();

        $listener = GuardListenerFactory::create('main', []);
        static::assertInstanceOf(GuardAuthenticationListener::class, $listener);
    }
}