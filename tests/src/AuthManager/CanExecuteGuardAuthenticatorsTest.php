<?php

namespace ByTIC\Auth\Tests\AuthManager;

use ByTIC\Auth\AuthManager;
use ByTIC\Auth\AuthServiceProvider;
use ByTIC\Auth\Services\JWTManager;
use ByTIC\Auth\Tests\AbstractTest;
use ByTIC\Auth\Tests\Fixtures\Security\Guard\AppCustomAuthenticator;
use ByTIC\Auth\Tests\Fixtures\Users\User;
use ByTIC\Auth\Tests\Fixtures\Users\Users;
use Lcobucci\JWT\Token\DataSet;
use Lcobucci\JWT\Token\Plain;
use Lcobucci\JWT\Token\Signature;
use Mockery\Mock;
use Nip\Config\Config;
use Nip\Container\Utility\Container;
use Nip\Http\Request;
use Nip\Records\Locator\ModelLocator;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;
use Symfony\Component\Security\Http\Authenticator\Token\PostAuthenticationToken;

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

    public function test_authRequestWith_jwt()
    {
        $container = Container::container();
        $this->loadConfigIntoContainer('basic');

        $serviceProvider = new AuthServiceProvider();
        $serviceProvider->setContainer($container);
        $serviceProvider->register();
        $jwtManager = \Mockery::mock(JWTManager::class)->makePartial();
        $jwtManager->shouldReceive('parse')->once()->andReturnUsing(
            function () {
                return new Plain(
                    new DataSet(['alg' => 'none'], 'headers'),
                    new DataSet(['sub' => 'users|1'], 'claims'),
                    new Signature('hash', 'signature')
                );
            }
        );
        $container->set(AuthServiceProvider::JWT_MANAGER, $jwtManager);

        $request = new \Symfony\Component\HttpFoundation\Request();
        $request->headers->set('Authorization', 'Bearer testtoken');

        $users = \Mockery::mock(Users::class)->makePartial();
        $users->shouldReceive('findOne')->once()->andReturn(new User());
        ModelLocator::set('users', $users);

        /** @var AuthManager|Mock $manager */
        $manager = \Mockery::mock(AuthManager::class)->makePartial()->shouldAllowMockingProtectedMethods();

        $result = $manager->authRequestWith('jwt', $request);
        self::assertInstanceOf(PostAuthenticationToken::class, $result);
    }
}