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

    public function getRoles(): array
    {
        return [];
    }

    /**
     * @return mixed|void|null
     */
    public function getPassword(): ?string
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

    /**
     * @inheritDoc
     */
    public function getUserIdentifier(): string
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
        $this->setAttribute('password', $password);
    }

    /**
     * Returns the user unique id.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->getAttribute('id');
    }

    public function getEmail()
    {
        return $this->getAttribute('email');
    }

    public function setEmail($email)
    {
        $this->setAttribute('email', $email);
    }

    public function hasRole($role)
    {
        return false;
    }

    public function setRoles(array $roles)
    {
        // TODO: Implement setRoles() method.
    }

    public function addRole($role)
    {
        // TODO: Implement addRole() method.
    }

    public function removeRole($role)
    {
        // TODO: Implement removeRole() method.
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