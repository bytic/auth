<?php

namespace ByTIC\Auth\Models\Users\PersistentData\Engines;

use ByTIC\Auth\Models\Users\Traits\Authentication\AuthenticationUserTrait;
use ByTIC\PersistentData\Engines\CookiesEngine;
use Nip\Records\Record;

/**
 * Class UserCookieEngine
 * @package ByTIC\Auth\Models\Users\PersistentData\Engines
 */
class UserCookieEngine extends CookiesEngine
{

    /**
     * @inheritDoc
     */
    protected function generateData()
    {
        $rawData = parent::generateData();
        if (is_string($rawData) && strpos($rawData, ':')) {
            list($id, $password) = explode(":", $rawData);
        } else {
            $id       = $rawData;
            $password = '';
        }

        return ['id' => $id, 'password' => $password];
    }

    /**
     * @param $model
     *
     * @return bool
     */
    protected function validateModelFoundFromData($model)
    {
        $return = parent::validateModelFoundFromData($model);
        if ($return !== true) {
            return $return;
        }
        $data = $this->getData();

        return $this->checkSaltedPassword($model, $data['password']);
    }

    /**
     * @param $data
     *
     * @return bool|int
     */
    protected function parseDataForModelFindParams($data)
    {
        if ( ! is_array($data) || ! isset($data['id']) || empty($data['id'])) {
            return false;
        }

        return intval($data['id']);
    }

    /**
     * @param Record|AuthenticationUserTrait $model
     *
     * @return mixed
     */
    protected function generateDataFromModel($model)
    {
        return $model->id . ':' . $this->hashPassword($model);
    }

    /**
     * @param $model
     * @param $password
     * @return bool
     */
    protected function checkSaltedPassword($model, $password): bool
    {
        return $this->hashPassword($model) === $password;
    }

    /**
     * @param $model
     * @return mixed
     */
    protected function hashPassword($model)
    {
        $helper = $model->getManager()->getPasswordHelper()->setSalt($model->salt);
        return $helper->hash($model->password);
    }
}
