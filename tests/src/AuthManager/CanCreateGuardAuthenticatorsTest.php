<?php

namespace ByTIC\Auth\Tests\AuthManager;

use ByTIC\Auth\AuthManager;
use ByTIC\Auth\Security\Guard\Authenticator\BaseAuthenticator;
use ByTIC\Auth\Tests\AbstractTest;
use ByTIC\Auth\Tests\Fixtures\Security\Guard\AppCustomAuthenticator;
use Mockery\Mock;

/**
 * Class CanCreateGuardAuthenticatorsTest
 * @package ByTIC\Auth\Tests\AuthManager
 */
class CanCreateGuardAuthenticatorsTest extends AbstractTest
{

    public function test_guardAuthenticator_empty()
    {
        /** @var AuthManager|Mock $manager */
        $manager = \Mockery::mock(AuthManager::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $authenticator = $manager->guardAuthenticator();

        self::assertInstanceOf(BaseAuthenticator::class, $authenticator);
    }

    public function test_guardAuthenticator_withClassName()
    {
        /** @var AuthManager|Mock $manager */
        $manager = \Mockery::mock(AuthManager::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $authenticator = $manager->guardAuthenticator(AppCustomAuthenticator::class);

        self::assertInstanceOf(AppCustomAuthenticator::class, $authenticator);
    }

    public function test_guardAuthenticator_recordClassName()
    {
        /** @var AuthManager|Mock $manager */
        $manager = \Mockery::mock(AuthManager::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $manager->shouldReceive('createGuardAuthenticator')->once()
            ->with('custom-authenticator')->andReturn(new AppCustomAuthenticator());

//        Container::getInstance()->set('custom-authenticator', new AppCustomAuthenticator());

        $authenticator1 = $manager->guardAuthenticator('custom-authenticator');
        $authenticator1->singleton = true;
        $authenticator2 = $manager->guardAuthenticator(AppCustomAuthenticator::class);

        self::assertInstanceOf(AppCustomAuthenticator::class, $authenticator1);
        self::assertInstanceOf(AppCustomAuthenticator::class, $authenticator2);
        self::assertTrue($authenticator2->singleton);
    }
}