<?php

namespace ByTIC\Auth\Models\Traits;

use Nip\Cookie\Jar as CookieJar;
use Nip\Records\Record as Record;

/**
 * Class PersistentCurrent
 * @package ByTIC\Common\Records\Traits
 *
 *
 * @method Record findOne($ID)
 * @method string getTable()
 */
trait PersistentCurrent
{
    /**
     * @var Record
     */
    protected $_current;

    /**
     * @return Record
     */
    public function getCurrent()
    {
        if ($this->_current === null) {
            $this->_current = false;

            $item = $this->getFromSession();
            if (!$item) {
                $item = $this->getFromCookie();
            }

            if ($item && $this->checkAccessCurrent($item)) {
                $this->beforeSetCurrent($item);
                $this->setAndSaveCurrent($item);
            } else {
                $this->setCurrent($this->getCurrentDefault());
            }
        }

        return $this->_current;
    }

    public function getCurrentDefault()
    {
        return false;
    }

    /**
     * @param Record|boolean $item
     * @return $this
     */
    public function setAndSaveCurrent($item = false)
    {
        $this->setCurrent($item);
        $this->savePersistCurrent($item);

        return $this;
    }

    public function beforeSetCurrent($item)
    {
    }

    /**
     * @param Record|boolean $item
     * @return $this
     */
    public function setCurrent($item = false)
    {
        $this->_current = $item;

        return $this;
    }

    /**
     * @param Record|boolean $item
     * @return $this
     */
    public function savePersistCurrent($item)
    {
        if (is_object($item)) {
            $this->savePersistentCurrent($item);
        } else {
            $this->removePersistentCurrent();
        }

        return $this;
    }

    /**
     * @param Record|boolean $item
     * @return $this
     */
    public function savePersistentCurrent($item)
    {
        $this->savePersistCurrentSession($item);
        $this->savePersistCurrentCookie($item);

        return $this;
    }

    /**
     * @param Record|boolean $item
     * @return void
     */
    public function savePersistCurrentSession($item)
    {
        $varName = $this->getCurrentVarName();
        $_SESSION[$varName] = $item->toArray();
    }

    /**
     * @param Record|boolean $item
     * @return void
     */
    public function savePersistCurrentCookie($item)
    {
        $varName = $this->getCurrentVarName();
        CookieJar::instance()->newCookie()->setName($varName)->setValue($item->id)->save();
    }

    /**
     * @return $this
     */
    public function removePersistentCurrent()
    {
        $varName = $this->getCurrentVarName();
        unset($_SESSION[$varName]);
        CookieJar::instance()->newCookie()->setName($varName)->setValue(0)->setExpire(time() - 1000)->save();

        return $this;
    }

    public function getFromSession()
    {
        $sessionInfo = $this->getCurrentSessionData();

        if (is_array($sessionInfo)) {
            if ($sessionInfo['id']) {
                $ID = intval($sessionInfo['id']);

                return $this->findOne($ID);
            }
        }

        return false;
    }

    public function getCurrentSessionData()
    {
        $varName = $this->getCurrentVarName();

        return $_SESSION[$varName];
    }

    public function getCurrentVarName()
    {
        return $this->getTable();
    }

    public function getFromCookie()
    {
        $varName = $this->getCurrentVarName();
        if ($_COOKIE[$varName]) {
            $id = $_COOKIE[$varName];

            $item = $this->findOne(intval($id));
            if ($item) {
                return $item;
            }
        }

        return false;
    }

    public function checkAccessCurrent($item)
    {
        return is_object($item);
    }
}