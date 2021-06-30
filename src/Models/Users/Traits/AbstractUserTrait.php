<?php

namespace ByTIC\Auth\Models\Users\Traits;

use ByTIC\Auth\Models\Users\Traits\Authentication\AuthenticationUserTrait;
use ByTIC\DataObjects\Behaviors\Timestampable\TimestampableTrait;

/**
 * Class AbstractUserTrait
 * @package ByTIC\Auth\Models\Users\Traits
 *
 * @property string $created
 * @property string $modified
 */
trait AbstractUserTrait
{
    use AuthenticationUserTrait;
    use TimestampableTrait;

    /**
     * @var string
     */
    static protected $createTimestamps = ['created'];

    /**
     * @var string
     */
    static protected $updateTimestamps = ['modified'];

    /**
     * @return string
     */
    public function getName()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getRoles()
    {
        return [];
    }

    /**
     * @return mixed|void|null
     */
    public function getPassword()
    {
        return $this->getAttribute('password');
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    /**
     * @return mixed|void|null
     */
    public function getUsername()
    {
        return $this->getAttribute('username');
    }

    public function getUserIdentifier()
    {
        return $this->getAttribute('email');
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * @param $password
     * @return void
     */
    public function setPassword(string $password)
    {
        return $this->setAttribute('password', $password);
    }

    /**
     * @return mixed
     */
    public function sendPasswordMail()
    {
        $email = new \User_Email_Recover();
        $email->setItem($this);

        return $email->save();
    }
}