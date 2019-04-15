<?php

namespace ByTIC\Auth\Models\Users\Traits;

use ByTIC\Auth\Models\Users\Traits\Authentication\AuthenticationUserTrait;

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

    /**
     * @return string
     */
    public function getName()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * @return mixed
     */
    public function insert()
    {
        /** @noinspection PhpUndefinedConstantInspection */
        $this->created = date(DATE_DB);

        /** @noinspection PhpUndefinedClassInspection */
        return parent::insert();
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

    public function update()
    {
        $this->modified = date(DATE_DB);
        return parent::update();
    }
}