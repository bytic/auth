<?php

namespace ByTIC\Auth\Manager;

use ByTIC\Auth\AuthServiceProvider;
use ByTIC\Auth\Services\JWTManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticatorManager;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Class AuthManager
 * @package ByTIC\Auth
 */
class AuthManager
{
    use Behaviours\CanCreateUserProviders;
    use Behaviours\CanCreateAuthenticators;
    use Behaviours\CanExecuteAuthenticators;
    use Behaviours\HasAuthenticatorManager;
    use Behaviours\HasTokenStorage;
    use Behaviours\HasEventDispatcher;

    /**
     * @var \Closure|AuthenticatorManager
     */
    protected $manager;

    public function __construct(
        ?iterable $authenticators,
        TokenStorageInterface $tokenStorage,
        EventDispatcherInterface $eventDispatcher,
        string $firewallName,
        LoggerInterface $logger = null,
        bool $eraseCredentials = true,
        bool $hideUserNotFoundExceptions = true,
        array $requiredBadges = []
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->setEventDispatcher($eventDispatcher);

        $this->createManagerWith(
            $authenticators,
            $firewallName,
            $logger,
            $eraseCredentials,
            $hideUserNotFoundExceptions,
            $requiredBadges
        );
    }


    /**
     * @return JWTManager
     */
    public function jwtManager(): JWTManager
    {
        return app(AuthServiceProvider::JWT_MANAGER);
    }

    /**
     * Helper to get the config values.
     *
     * @param string $key
     * @param string $default
     *
     * @return mixed
     * @throws \Exception
     */
    protected function config($key, $default = null)
    {
        return config("auth.$key", $default);
    }
}