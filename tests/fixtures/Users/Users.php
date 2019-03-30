<?php

namespace ByTIC\Auth\Tests\Fixtures\Users;

use ByTIC\Auth\Models\Users\Traits\AbstractUsersTrait;
use Nip\Records\RecordManager;

/**
 * Class Users
 * @package ByTIC\Auth\Tests\Fixtures\Users
 */
class Users extends RecordManager
{
    use AbstractUsersTrait;

    /** @noinspection PhpMissingParentCallCommonInspection
     * @inheritDoc
     */
    public function getPrimaryKey()
    {
        return 'id';
    }

    /** @noinspection PhpMissingParentCallCommonInspection
     * @inheritDoc
     */
    public function getTable()
    {
        return 'users';
    }
}
