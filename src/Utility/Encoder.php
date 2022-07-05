<?php

namespace ByTIC\Auth\Utility;

use ByTIC\Auth\AuthServiceProvider;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class Encoder
 * @package ByTIC\Auth\Utility
 */
class Encoder
{
    /**
     * @return UserPasswordHasherInterface
     */
    public static function encoder()
    {
        return app(AuthServiceProvider::ENCODER);
    }

    /**
     * @return PasswordHasherFactoryInterface
     */
    public static function factory()
    {
        return app(AuthServiceProvider::ENCODERS_FACTORY);
    }
}