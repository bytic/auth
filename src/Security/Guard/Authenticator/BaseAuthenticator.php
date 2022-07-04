<?php

namespace ByTIC\Auth\Security\Guard\Authenticator;

use ByTIC\Auth\Utility\Encoder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\PasswordAuthenticatedInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;

/**
 * Class BaseAuthenticator
 * @package ByTIC\Auth\Security\Guard\Authenticator
 */
class BaseAuthenticator extends AbstractAuthenticator implements PasswordAuthenticatedInterface
{
    /**
     * @var UserPasswordEncoderInterface
     */
    protected $encoderFactory;

    public function __construct()
    {
        $this->encoderFactory = Encoder::factory();
    }

    public function authenticate(Request $request)
    {
        $credentials = $this->getCredentials($request);
    }

    /**
     * @inheritDoc
     */
    public function supports(Request $request): ?bool
    {
        return $request->request->has('_username') && $request->request->has('_password');
    }

    /**
     * @inheritDoc
     */
    public function getCredentials(Request $request)
    {
        return [
            'username' => $request->request->get('_username'),
            'password' => $request->request->get('_password'),
        ];
    }

//    /**
//     * @inheritDoc
//     */
//    public function getUser($credentials, UserProviderInterface $userProvider)
//    {
//        if (null === $credentials) {
//            // The token header was empty, authentication fails with HTTP Status
//            // Code 401 "Unauthorized"
//            return null;
//        }
//
//        // The "username" in this case is the apiToken, see the key `property`
//        // of `your_db_provider` in `security.yaml`.
//        // If this returns a user, checkCredentials() is called next:
//        return $userProvider->loadUserByIdentifier($credentials['username']);
//    }

//    /**
//     * @inheritDoc
//     */
//    public function getPassword($credentials): ?string
//    {
//        return $credentials['password'];
//    }

//    /**
//     * @inheritDoc
//     */
//    public function checkCredentials($credentials, UserInterface $user)
//    {
//        $encoder = $this->encoderFactory->getEncoder($user);
//
//        return $encoder->isPasswordValid($user->getPassword(), $credentials['password'], $user->getSalt());
//    }

//    /**
//     * @inheritDoc
//     */
//    public function supportsRememberMe()
//    {
//        // TODO: Implement supportsRememberMe() method.
//    }
}