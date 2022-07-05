<?php

namespace ByTIC\Auth\Tests\AuthManager;

use ByTIC\Auth\Manager\AuthManager;
use ByTIC\Auth\Security\Core\UserProvider\IdentifierUserProvider;
use ByTIC\Auth\Tests\AbstractTest;
use Mockery\Mock;

/**
 * Class CanCreateUserProvidersTest
 * @package ByTIC\Auth\Tests\AuthManager
 */
class CanCreateUserProvidersTest extends AbstractTest
{

    public function test_userProvider_noConfig()
    {
        /** @var AuthManager|Mock $manager */
        $manager = \Mockery::mock(AuthManager::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $manager->shouldReceive('getDefaultUserProvider')->andReturn('identifier');
        $manager->shouldReceive('config')->once()->andReturnNull();

        $provider1 = $manager->userProvider();
        $provider1->singleton = true;

        $provider2 = $manager->userProvider();
        self::assertInstanceOf(IdentifierUserProvider::class, $provider1);
        self::assertEquals($provider1, $provider2);
        self::assertTrue($provider2->singleton);
    }

    public function test_userProvider_callCreateOnce()
    {
        /** @var AuthManager|Mock $manager */
        $manager = \Mockery::mock(AuthManager::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $manager->shouldReceive('createUserProvider')->once()->andReturnNull();

        $manager->userProvider('user');
        $provider = $manager->userProvider('user');

        self::assertFalse($provider);
    }
}
