<?php

namespace ByTIC\Auth;

use ByTIC\Auth\Security\Core\Encoder\EncoderFactory;
use ByTIC\Auth\Services\JWTManager;
use Nip\Config\Config;
use Nip\Container\ServiceProviders\Providers\AbstractSignatureServiceProvider;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\User\UserChecker;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

/**
 * Class AuthServiceProvider
 * @package ByTIC\Auth
 */
class AuthServiceProvider extends AbstractSignatureServiceProvider
{
    public const ENCODERS_FACTORY = 'auth.encoders_factory';
    public const ENCODER = 'auth.encoder';
    public const JWT_MANAGER = 'auth.jwt.manager';

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->registerManager();
        $this->registerUserProvider();
        $this->registerUserChecker();
        $this->registerGuardHandler();
        $this->registerTokenStorage();
        $this->registerEncoderFactory();
        $this->registerEncoder();
        $this->registerJwtManager();
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
     * Register a resolver for the authenticated user.
     *
     * @return void
     */
    protected function registerUserChecker()
    {
        $this->getContainer()->share(
            'auth.user_checker',
            function () {
                return new UserChecker();
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

    protected function registerEncoderFactory()
    {
        $this->getContainer()->share(
            self::ENCODERS_FACTORY,
            function () {
                $encoders = config("auth.encoders", [
                    \Symfony\Component\Security\Core\User\UserInterface::class => [
                        'algorithm' => 'auto',
                        'hash_algorithm' => 'sha256',
                        'cost' => 12,
                    ],
                ]);

                return new EncoderFactory($encoders instanceof Config ? $encoders->toArray() : $encoders);
            }
        );
    }

    protected function registerEncoder()
    {
        $this->getContainer()->share(
            self::ENCODER,
            function () {
                return new UserPasswordEncoder(app('auth.encoders_factory'));
            }
        );
    }

    protected function registerJwtManager()
    {
        $this->getContainer()->share(
            self::JWT_MANAGER,
            function () {
                return new JWTManager();
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
            'auth.user_checker',
            'auth.token_storage',
            'auth.guard_handler',
            'auth.provider_manager',
            self::ENCODERS_FACTORY,
            self::ENCODER,
            self::JWT_MANAGER,
        ];
    }
}