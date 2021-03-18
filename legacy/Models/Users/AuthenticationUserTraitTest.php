<?php

namespace ByTIC\Auth\Legacy\Models\Users;

use Nip\HelperBroker;
use Nip_Helper_Passwords as PasswordsHelper;

/**
 * Trait AuthenticationUserTrait
 * @package ByTIC\Auth\LegacyModels\Users
 * @deprecated
 */
trait AuthenticationUserTrait
{
    /**
     * @return PasswordsHelper
     * @deprecated
     */
    public function getPasswordHelper()
    {
        return HelperBroker::instance()->getByName('Passwords');
    }
}
