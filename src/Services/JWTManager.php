<?php

namespace ByTIC\Auth\Services;

use DateTimeZone;
use Exception;
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Token\Plain;
use Lcobucci\JWT\Validation\Constraint\LooseValidAt;
use Lcobucci\JWT\Validation\Constraint\SignedWith;

use function date_default_timezone_get;

/**
 * Class JWTManager
 * @package ByTIC\Auth\Services
 */
class JWTManager
{

    /**
     * @param string $tokenString
     * @param null $key
     * @return Token|Plain
     * @throws Exception
     */
    public function parse(string $tokenString, $key = null)
    {
        $key = $key ?: (app()->has('oauth.keys.public') ? app('oauth.keys.public') : null);
        if (empty($key)) {
            throw new Exception("You need to define oauth keys in container [oauth.keys.public]");
        }

        $jwtConfiguration = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText('')
        );

        $jwtConfiguration->setValidationConstraints(
            new LooseValidAt(new SystemClock(new DateTimeZone(date_default_timezone_get()))),
            new SignedWith(new Sha256(), InMemory::plainText($key))
        );

        $token = $jwtConfiguration->parser()->parse($tokenString);
        if (false === ($token instanceof Plain)) {
            throw new Exception("Invalid oauth Token");
        }

        $constraints = $jwtConfiguration->validationConstraints();
        if (!$jwtConfiguration->validator()->validate($token, ...$constraints)) {
            throw new Exception('No way!');
        }
        return $token;
    }
}