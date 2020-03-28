<?php

namespace ByTIC\Auth\Security\Core\UserProvider;

use ByTIC\Auth\Models\Users\Resolvers\UsersResolvers;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class IdentifierUserProvider
 * @package ByTIC\Auth\Security\Core\UserProvider
 */
class IdentifierUserProvider implements UserProviderInterface
{

    /**
     * @inheritDoc
     */
    public function loadUserByUsername($username)
    {
        $user = UsersResolvers::resolve($username);

        if ($user !== null) {
            return $user;
        }

        throw new UsernameNotFoundException(
            sprintf('Username "%s" does not exist.', $username)
        );
    }

    /**
     * @inheritDoc
     */
    public function refreshUser(UserInterface $user)
    {
//        if (!$user instanceof User) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', \get_class($user))
            );
//        }
//        $username = $user->getUsername();
//        return $this->findUsername($username);
    }

    /**
     * @inheritDoc
     */
    public function supportsClass($class)
    {
//        return User::class === $class;
    }
}