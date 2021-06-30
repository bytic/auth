<?php

namespace ByTIC\Auth\Tests\Fixtures\Users;

use ByTIC\Auth\Models\Users\Traits\AbstractUserTrait;
use Nip\Records\Record;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class User
 * @package ByTIC\Auth\Tests\Fixtures\Users
 * @method string getUserIdentifier()
 */
class User extends Record implements UserInterface
{
    use AbstractUserTrait;
}
