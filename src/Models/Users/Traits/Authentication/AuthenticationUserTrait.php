<?php

namespace ByTIC\Auth\Models\Users\Traits\Authentication;

use ByTIC\Auth\Models\Users\Traits\AbstractUserTrait as User;
use Nip\HelperBroker;
use Nip_Helper_Passwords as PasswordsHelper;

/**
 * Class AuthenticationUserTrait
 * @package ByTIC\Common\Records\Users\Authentication
 *
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $password
 * @property string $email
 * @property string $salt
 * @property string $last_login
 *
 * @property string $new_password
 *
 * @method AuthenticationUsersTrait getManager()
 */
trait AuthenticationUserTrait
{
    protected $authenticated = false;

    /**
     * Authenticate user from request
     *
     * @param array $request
     * @return bool|null
     */
    public function authenticate($request = [])
    {
        if ($request) {
            $this->email = clean($request['email']);
            $this->password = $this->getPasswordHelper()->hash(clean($request['password']));
        } else {
            $this->password = $this->getPasswordHelper()->hash(clean($this->password));
        }

        $query = $this->getManager()->newQuery();

        $query->where("email = ?", $this->email);
        $query->where("password = ?", $this->password);

        /** @var User $user */
        $user = $this->getManager()->findOneByQuery($query);

        if ($user) {
            $this->writeData($user->toArray());
            $this->doAuthentication();
        }

        return $this->authenticated();
    }

    /**
     * @return PasswordsHelper
     */
    public function getPasswordHelper()
    {
        return HelperBroker::instance()->getByName('Passwords');
    }

    public function doAuthentication()
    {
        /** @noinspection PhpUndefinedConstantInspection */
        $this->last_login = date(DATE_DB);
        $this->save();

        $this->authenticated(true);
        $this->getManager()->setAndSaveCurrent($this);
    }

    /**
     * @param null $value
     * @return bool|null
     */
    public function authenticated($value = null)
    {
        if (!is_null($value)) {
            $this->authenticated = $value;
        }

        return $this->authenticated;
    }

    /**
     * @return $this
     */
    public function deauthenticate()
    {
        $this->getManager()->setAndSaveCurrent(null);

        $this->authenticated(false);

        return $this;
    }

    /**
     * @return $this
     */
    public function register()
    {
        $this->new_password = $this->password;

//        $this->generateSalt();
        $this->hashPassword();
        $this->insert();

        return $this;
    }

    public function hashPassword()
    {
        $this->password = $this->getPasswordHelper()->hash($this->new_password);
    }

    /**
     * @return $this
     */
    public function generateSalt()
    {
        $this->salt = md5(uniqid("", true));

        return $this;
    }

    /**
     * @param $password
     * @return bool
     */
    public function checkSaltedPassword($password)
    {
        $helper = $this->getPasswordHelper()->setSalt($this->salt);
        $helper->hash($this->password);

        return $password == $helper->hash($this->password);
    }

    /**
     * @param array $request
     * @return bool
     */
    public function validatePasswordRecovery($request = [])
    {
        if ($request) {
            $this->email = clean($request['email']);
        }

        if (!$this->email || !valid_email($this->email) || !$this->exists()) {
            return false;
        }

        return true;
    }

    public function recoverPassword()
    {
        /** @var User $user */
        $user = $this->getManager()->findOneByEmail($this->email);
        $this->writeData($user->toArray());

        $this->generatePassword();
        $this->hashPassword();
        $this->update();
        $this->password = $this->new_password;

        $this->sendPasswordMail();
    }

    /**
     * @return $this
     */
    public function generatePassword()
    {
        $this->new_password = $this->getPasswordHelper()->generate(8, false, true, true, false);

        return $this;
    }

    /**
     * @return mixed
     */
    abstract public function sendPasswordMail();
}
