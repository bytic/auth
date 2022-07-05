<?php

namespace ByTIC\Auth\Manager\Behaviours;

use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticatorManager;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 *
 */
trait HasAuthenticatorManager
{
    /**
     * @var AuthenticatorManager
     */
    protected $manager;

    public function createManagerWith(
        ?iterable $authenticators,
        TokenStorageInterface $tokenStorage,
        EventDispatcherInterface $eventDispatcher,
        string $firewallName,
        LoggerInterface $logger = null,
        bool $eraseCredentials = true,
        bool $hideUserNotFoundExceptions = true,
        array $requiredBadges = []
    ) {
        $this->manager = function () use (
            $authenticators,
            $tokenStorage,
            $eventDispatcher,
            $firewallName,
            $logger,
            $eraseCredentials,
            $hideUserNotFoundExceptions,
            $requiredBadges
        ) {
            $authenticators = $authenticators ?: [$this->authenticator()];
            return new AuthenticatorManager(
                $authenticators,
                $tokenStorage,
                $eventDispatcher,
                $firewallName,
                $logger,
                $eraseCredentials,
                $hideUserNotFoundExceptions,
                $requiredBadges
            );
        };
    }

    public function manager()
    {
        if ($this->manager instanceof \Closure) {
            $this->manager = ($this->manager)();
        }
        return $this->manager;
    }

    /**
     * @param AuthenticatorManager $manager
     */
    public function setManager(AuthenticatorManager $manager)
    {
        $this->manager = $manager;
    }
}