<?php

namespace ByTIC\Auth\Models\Users\Traits\Authentication;

use ByTIC\Auth\Models\Users\Resolvers\UsersResolvers;
use ByTIC\Auth\Models\Users\Traits\Authentication\AuthenticationUserTrait as User;
use Lcobucci\JWT\Parser as JwtParser;
use Lcobucci\JWT\Signer\Rsa\Sha256;
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
        $jwt = new JwtParser();

        $token = $jwt->parse($tokenString);
        $key = $key ? $key : (app()->has('oauth.keys.public') ? app('oauth.keys.public') : null);
        if (empty($key)) {
            throw new \Exception("You need to define oauth keys in container [oauth.keys.public]");
        }

        $signer = new Sha256();
        $validJwt = $token->verify($signer, $key);

        if ($validJwt !== true) {
            throw new \Exception("Invalid oauth Token");
        }

        $entity = UsersResolvers::resolve($token->getClaim('sub'));
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
