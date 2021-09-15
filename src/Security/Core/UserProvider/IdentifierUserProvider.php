<?php

namespace ByTIC\Auth\Security\Core\UserProvider;

use ByTIC\Auth\Models\Users\Resolvers\UsersResolvers;
use ByTIC\Auth\Models\Users\Traits\AbstractUserTrait;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class IdentifierUserProvider
 * @package ByTIC\Auth\Security\Core\UserProvider
 */
class IdentifierUserProvider implements UserProviderInterface, PasswordUpgraderInterface
{
    /**
     * @inheritDoc
     */
    public function loadUserByUsername(string $username)
    {
        return $this->loadUserByIdentifier($username);
    }

    /**
     * @inheritDoc
     */
    public function loadUserByIdentifier(string $username)
    {
        $user = UsersResolvers::resolveByUsername($username);

        if ($user !== null) {
            return $user;
        }

        throw new UserNotFoundException(
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

    /**
     * @param UserInterface|AbstractUserTrait $user
     * @param string $newEncodedPassword
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        // set the new encoded password on the User object
        $user->setPassword($newEncodedPassword);
        $user->save();
    }
}