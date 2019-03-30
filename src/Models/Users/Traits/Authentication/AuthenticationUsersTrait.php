<?php

namespace ByTIC\Auth\Models\Users\Traits\Authentication;

use ByTIC\Auth\Models\Users\PersistentData\Engines\UserCookieEngine;
use ByTIC\Auth\Models\Users\Traits\Authentication\AuthenticationUserTrait as User;
use ByTIC\PersistentData\PersistentManagerTrait;
use Nip\HelperBroker;
use Nip_Helper_Passwords as PasswordsHelper;

/**
 * Class AuthenticationUsersTrait
 * @package ByTIC\Common\Records\Users\Authentication
 *
 * @method User findOneByEmail($email)
 * @method User getCurrent()
 */
trait AuthenticationUsersTrait
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
     * @return bool|\Nip\Records\Record|User
     */
    public function getFromCookie()
    {
        if (isset($_COOKIE['login'])) {
            list($id, $password) = explode(":", $_COOKIE['login']);

            /** @var User $user */
            $user = $this->findOne(intval($id));
            if ($user) {
                if ($user->checkSaltedPassword($password)) {
                    return $user;
                }
            }
        }

        return false;
    }

    /**
     * @param User $user
     */
    public function savePersistCurrentSession($user)
    {
        $varName = $this->getCurrentVarName();
        $data = $user->toArray();
        unset($data['_forms']);
        unset($data['password_repeat']);
        $_SESSION[$varName] = $data;
    }

    /**
     * @return \Nip\Helpers\AbstractHelper|PasswordsHelper
     */
    public function getPasswordHelper()
    {
        return HelperBroker::instance()->getByName('Passwords');
    }

    /**
     * @return array
     */
    protected function getPersistentDataEnginesTypes()
    {
        return ['session', UserCookieEngine::class];
    }
}
