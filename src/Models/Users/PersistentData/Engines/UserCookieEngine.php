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
        if (strpos($rawData, ':')) {
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

        return $model->checkSaltedPassword($data['password']);
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
        $helper = $model->getManager()->getPasswordHelper()->setSalt($model->salt);

        return $model->id . ':' . $helper->hash($model->password);
    }
}
