<?php

namespace ByTIC\Auth\Models\Users\Traits\Persistance;

use ByTIC\Auth\Models\Users\PersistentData\Engines\UserCookieEngine;
use ByTIC\Auth\Models\Users\Traits\Authentication\AuthenticationUserTrait as User;
use ByTIC\PersistentData\PersistentManagerTrait;

/**
 * Trait PersistentUsersTrait
 * @package ByTIC\Auth\Models\Users\Traits\Persistance
 */
trait PersistentUsersTrait
{
    use PersistentManagerTrait;

    /**
     * @param User $item
     */
    public function beforeSetCurrent($item)
    {
        $item->authenticated(true);
    }

    /**
     * @return \Nip\Records\Record|User
     */
    public function getCurrentDefault()
    {
        return $this->getNew();
    }

    /**
     * @return array
     */
    protected function getPersistentDataEnginesTypes()
    {
        return ['session', UserCookieEngine::class];
    }
}