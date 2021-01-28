<?php

namespace ByTIC\Auth\Models\Users\Traits\Authentication;

use ByTIC\Auth\Models\Users\Resolvers\UsersResolvers;
use ByTIC\Auth\Models\Users\Traits\Authentication\AuthenticationUserTrait as User;
use DateTimeZone;
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token\Plain;
use Lcobucci\JWT\Validation\Constraint\LooseValidAt;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Nip\HelperBroker;
use Nip_Helper_Passwords as PasswordsHelper;

/**
 * Class AuthenticationUsersTrait
 * @package ByTIC\Common\Records\Users\Authentication
 *
 * @method User findOneByEmail($email)
 * @method User getCurrent()
 */
trait AuthenticationUsersTrait
{

    /**
     * @param string $tokenString
     * @param null|string $key
     * @throws \Exception
     */
    public function authenticateWithToken($tokenString, $key = null)
    {
        $key = $key ? $key : (app()->has('oauth.keys.public') ? app('oauth.keys.public') : null);
        if (empty($key)) {
            throw new \Exception("You need to define oauth keys in container [oauth.keys.public]");
        }

        $jwtConfiguration = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText('')
        );

        $jwtConfiguration->setValidationConstraints(
            new LooseValidAt(new SystemClock(new DateTimeZone(\date_default_timezone_get()))),
            new SignedWith(new Sha256(), InMemory::plainText($key))
        );

        $token = $jwtConfiguration->parser()->parse($tokenString);
        if (!($token instanceof Plain)) {
            throw new \Exception("Invalid oauth Token");
        }

        $constraints = $jwtConfiguration->validationConstraints();
        if (!$jwtConfiguration->validator()->validate($token, ...$constraints)) {
            throw new \Exception('No way!');
        }

        $claims = $token->claims();
        $entity = UsersResolvers::resolve($claims->get('sub'));
        if (get_class($entity) != $this->getModel()) {
            throw new \Exception("Invalid user type in token");
        }
        $this->setAndSaveCurrent($entity);
    }

    /**
     * @return \Nip\Helpers\AbstractHelper|PasswordsHelper
     */
    public function getPasswordHelper()
    {
        return HelperBroker::instance()->getByName('Passwords');
    }
}
