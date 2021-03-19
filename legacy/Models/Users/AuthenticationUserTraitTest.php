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

    /**
     * @param $password
     * @return bool
     * @deprecated
     */
    public function checkSaltedPassword($password)
    {
        $helper = $this->getPasswordHelper()->setSalt($this->salt);
        $helper->hash($this->password);

        return $password == $helper->hash($this->password);
    }
}
