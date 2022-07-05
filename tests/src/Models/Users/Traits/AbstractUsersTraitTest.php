<?php

namespace ByTIC\Auth\Tests\Models\Users\Traits;

use ByTIC\Auth\Tests\AbstractTest;
use ByTIC\Auth\Tests\Fixtures\Users\User;
use ByTIC\Auth\Tests\Fixtures\Users\Users;
use Nip\Records\Locator\ModelLocator;

/**
 * Class AbstractUsersTraitTest
 * @package ByTIC\Auth\Tests\Models\Users\Traits
 */
class AbstractUsersTraitTest extends AbstractTest
{
    public function testGetCurrentBlank()
    {
        $users = new Users();

        $current = $users->getCurrent();
        self::assertInstanceOf(User::class, $current);
        self::assertEmpty($current->id);
        self::assertFalse($current->isInDB());
        self::assertFalse($current->authenticated());
    }

    public function testGetCurrentFromSession()
    {
        $data = [
            'id' => '9',
        ];
        $user = new User();
        $user->writeData($data);

        $users = \Mockery::mock(Users::class)->makePartial();
        $users->shouldReceive('findOne')->with(9)->andReturn($user);
        $users->shouldReceive('savePersistCurrent')->with($user);
        ModelLocator::set(get_class($users), $users);

        $_SESSION['users'] = $data;

        /** @var User $current */
        $current = $users->getCurrent();

        self::assertInstanceOf(User::class, $current);
        self::assertEquals(9, $current->getId());
        self::assertTrue($current->isInDB());
        self::assertTrue($current->authenticated());

        unset($_SESSION['users']);
    }

    public function testGetCurrentFromCookie()
    {
        $_COOKIE['users'] = '9:##########';
        $user = \Mockery::mock(User::class)->makePartial();
        $user->shouldReceive('checkSaltedPassword')->with('##########')->andReturn(true)->once();
        $user->id = 9;

        $users = \Mockery::mock(Users::class)->makePartial();
        $users->shouldReceive('findOne')->with(9)->andReturn($user);
        $users->shouldReceive('savePersistCurrent')->with($user);
        ModelLocator::set(get_class($users), $users);

        /** @var User $current */
        $current = $users->getCurrent();

        self::assertInstanceOf(User::class, $current);
        self::assertEquals(9, $current->id);
        self::assertTrue($current->authenticated());

        unset($_COOKIE['users']);
    }
}
