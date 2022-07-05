<?php

namespace ByTIC\Auth\Manager\Behaviours;


use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 *
 */
trait HasTokenStorage
{
    protected TokenStorageInterface $tokenStorage;

    public function tokenStorage(): TokenStorageInterface
    {
        return $this->tokenStorage;
    }

    public function currentToken(): ?TokenInterface
    {
        return $this->tokenStorage->getToken();
    }
}
