<?php

namespace ByTIC\Auth\Utility;

use ByTIC\Auth\AuthServiceProvider;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class Encoder
 * @package ByTIC\Auth\Utility
 */
class Encoder
{
    /**
     * @return UserPasswordEncoderInterface
     */
    public static function encoder()
    {
        return app(AuthServiceProvider::ENCODER);
    }

    /**
     * @return EncoderFactoryInterface
     */
    public static function factory()
    {
        return app(AuthServiceProvider::ENCODERS_FACTORY);
    }
}