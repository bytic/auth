<?php

namespace ByTIC\Auth\AuthManager;

use Symfony\Component\Security\Guard\Token\PreAuthenticationGuardToken;

/**
 * Trait CanExecuteGuardAuthenticators
 * @package ByTIC\Auth\AuthManager
 */
trait CanExecuteGuardAuthenticators
{
    /**
     * @param $authenticator
     * @param $request
     * @return bool
     */
    public function authRequestWith($authenticator, $request)
    {
        $guardAuthenticator = $this->guardAuthenticator($authenticator);
        $uniqueGuardKey = 'auth-'.$authenticator;

        if ($guardAuthenticator->supports($request) === false) {
            return false;
        }

        // allow the authenticator to fetch authentication info from the request
        $credentials = $guardAuthenticator->getCredentials($request);

        if (null === $credentials) {
            throw new \UnexpectedValueException(sprintf('The return value of "%1$s::getCredentials()" must not be null. Return false from "%1$s::supports()" instead.',
                \get_class($guardAuthenticator)));
        }

        // create a token with the unique key, so that the provider knows which authenticator to use
        $token = new PreAuthenticationGuardToken($credentials, $uniqueGuardKey);

        // pass the token into the AuthenticationManager system
        // this indirectly calls GuardAuthenticationProvider::authenticate()
        // get the user from the GuardAuthenticator
        $user = $guardAuthenticator->getUser($token->getCredentials(), $this->userProvider);

        // turn the UserInterface into a TokenInterface
        $authenticatedToken = $guardAuthenticator->createAuthenticatedToken($user, $uniqueGuardKey);

        // sets the token on the token storage, etc
        $this->guardHandler->authenticateWithToken($token, $request, $this->providerKey);
    }
}