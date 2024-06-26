<?php

namespace ByTIC\Auth\Tests\Fixtures\Users;

use ByTIC\Auth\Models\Users\Traits\AbstractUserTrait;
use ByTIC\Auth\Users\UserInterface;
use Nip\Records\Record;

/**
 * Class User
 * @package ByTIC\Auth\Tests\Fixtures\Users
 */
class User extends Record implements UserInterface
{
    use AbstractUserTrait;


}
