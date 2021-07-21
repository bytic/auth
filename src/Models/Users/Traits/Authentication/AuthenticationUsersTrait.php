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
    public function findOneByUsername($identifier)
    {
        return $this->findOneByField('email', $identifier);
    }

    /**
     * @param string $tokenString
     * @param null|string $key
     * @throws \Exception
     */
    public function authenticateWithToken($tokenString, $key = null)
    {
        $token = auth()->jwtManager()->parse($tokenString, $key);

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
