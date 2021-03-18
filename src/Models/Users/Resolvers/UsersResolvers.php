<?php

namespace ByTIC\Auth\Models\Users\Resolvers;

use ByTIC\Auth\Models\Users\Traits\AbstractUserTrait;
use Nip\Records\Locator\ModelLocator;
use Nip\Records\Record;

/**
 * Class UsersResolvers
 * @package ByTIC\Auth\Models\Users\Resolvers
 */
class UsersResolvers
{
    const SEPARATOR = '|';

    /**
     * @param AbstractUserTrait|Record $entity
     * @return string
     */
    public static function identifier(Record $entity)
    {
        return $entity->getManager()->getTable().static::SEPARATOR.$entity->getPrimaryKey();
    }

    /**
     * @param string $identifier
     * @return string
     */
    public static function resolve(string $identifier)
    {
        list($userTable, $userIdentifier) = static::parseIdentifier($identifier);

        return static::resolveEntity($userTable, $userIdentifier);
    }

    /**
     * @param string $identifier
     * @return string
     */
    public static function resolveByUsername(string $identifier)
    {
        list($userTable, $userIdentifier) = static::parseIdentifier($identifier);

        return static::resolveEntity($userTable, $userIdentifier, 'username');
    }


    /**
     * @param $userTable
     * @param $userIdentifier
     * @return \Nip\Records\AbstractModels\Record
     */
    protected static function resolveEntity($userTable, $userIdentifier, $field = null)
    {
        $userRepository = ModelLocator::get($userTable);

        if (!empty($field)) {
            $method = 'findOneBy'. ucfirst($field);
            return $userRepository->$method($userIdentifier);
        }
        return $userRepository->findOne($userIdentifier);
    }

    /**
     * @param string $identifier
     * @return array
     */
    protected static function parseIdentifier($identifier)
    {
        if (strpos($identifier, static::class)) {
            $identifier = static::class.$identifier;
        }
        list($userTable, $userIdentifier) = explode(static::SEPARATOR, $identifier);
        $userTable = empty($userTable) ? 'users' : $userTable;

        return [$userTable, $userIdentifier];
    }
}
