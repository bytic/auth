<?php

namespace ByTIC\Auth\Security\Guard;

use Nip\Http\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserChecker;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Guard\PasswordAuthenticatedInterface;
use Symfony\Component\Security\Guard\Token\PreAuthenticationGuardToken;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;

use function get_class;
use function gettype;
use function is_object;

/**
 * Class GuardAuthenticatorInvoker
 * @package ByTIC\Auth\Security\Guard
 */
class GuardAuthenticatorInvoker
{
    /**
     * @var AbstractGuardAuthenticator
     */
    protected $authenticator;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var UserProviderInterface
     */
    protected $userProvider;

    /**
     * @var UserChecker
     */
    protected $userChecker;

    /**
     * @var UserPasswordEncoder
     */
    protected $passwordEncoder;

    /**
     * @var string
     */
    protected $providerKey;

    /**
     * GuardAuthenticatorInvoker constructor.
     * @param $authenticator
     * @param $request
     */
    public function __construct($authenticator, $request)
    {
        $this->setAuthenticator(
            is_object($authenticator) ? $authenticator : app('auth')->guardAuthenticator($authenticator)
        );

        $this->setRequest($request);
        $this->userProvider = app('auth.user_provider');
        $this->userChecker = app('auth.user_checker');
        $this->providerKey = 'auth-'.get_class($this->getAuthenticator());
        $this->passwordEncoder = new UserPasswordEncoder(app('auth.encoders_factory'));
    }

    /**
     * @return bool|\Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken
     */
    public function __invoke()
    {
        return $this->run();
    }

    /**
     * @return bool|\Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken
     */
    public function run()
    {
        if ($this->canRun() === false) {
            return false;
        }

        $user = $this->getUser();
        return $this->createAuthenticatedToken($user);
    }

    /**
     * @return bool
     */
    protected function canRun()
    {
        return $this->getAuthenticator()->supports($this->getRequest());
    }

    /**
     * @return UserInterface|null
     */
    protected function getUser()
    {
        $token = $this->getPreToken();

        // get the user from the GuardAuthenticator
        $user = $this->getAuthenticator()->getUser($token->getCredentials(), app('auth.user_provider'));

        if (null === $user) {
            throw new UsernameNotFoundException(sprintf('Null returned from %s::getUser()',
                get_class($this->getAuthenticator())));
        }

        if (!$user instanceof UserInterface) {
            throw new \UnexpectedValueException(sprintf('The %s::getUser() method must return a UserInterface. You returned %s.',
                get_class($this->getAuthenticator()), is_object($user) ? get_class($user) : gettype($user)));
        }

        $this->checkUser($user, $token);

        return $user;
    }

    /**
     * @param UserInterface $user
     * @param PreAuthenticationGuardToken $token
     */
    protected function checkUser($user, $token)
    {
        $guardAuthenticator = $this->getAuthenticator();
        $this->userChecker->checkPreAuth($user);

        if (true !== $guardAuthenticator->checkCredentials($token->getCredentials(), $user)) {
            throw new BadCredentialsException(sprintf('Authentication failed because %s::checkCredentials() did not return true.',
                get_class($this->getAuthenticator())));
        }

        if ($this->userProvider instanceof PasswordUpgraderInterface
            && $guardAuthenticator instanceof PasswordAuthenticatedInterface
            && null !== $this->passwordEncoder
            && (null !== $password = $guardAuthenticator->getPassword($token->getCredentials()))
            && method_exists($this->passwordEncoder, 'needsRehash')
            && $this->passwordEncoder->needsRehash($user)) {
            $this->userProvider->upgradePassword($user, $this->passwordEncoder->encodePassword($user, $password));
        }
        $this->userChecker->checkPostAuth($user);
    }

    /**
     * @param $user
     * @return \Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken
     */
    protected function createAuthenticatedToken($user)
    {
        // turn the UserInterface into a TokenInterface
        $authenticatedToken = $this->getAuthenticator()->createAuthenticatedToken($user, $this->providerKey);
        if (!$authenticatedToken instanceof TokenInterface) {
            throw new \UnexpectedValueException(sprintf('The %s::createAuthenticatedToken() method must return a TokenInterface. You returned %s.',
                get_class($this->getAuthenticator()),
                is_object($authenticatedToken) ? get_class($authenticatedToken) : gettype($authenticatedToken)));
        }

        return $authenticatedToken;
    }


    /**
     * @return PreAuthenticationGuardToken
     */
    protected function getPreToken()
    {
        $credentials = $this->getCredentials();

        // create a token with the unique key, so that the provider knows which authenticator to use
        $token = new PreAuthenticationGuardToken($credentials, $this->providerKey);

        return $token;
    }

    /**
     * @return mixed
     */
    protected function getCredentials()
    {
        // allow the authenticator to fetch authentication info from the request
        $credentials = $this->getAuthenticator()->getCredentials($this->getRequest());

        if (null === $credentials) {
            throw new \UnexpectedValueException(sprintf('The return value of "%1$s::getCredentials()" must not be null. Return false from "%1$s::supports()" instead.',
                get_class($this->getAuthenticator())));
        }

        return $credentials;
    }

    /**
     * @param AbstractGuardAuthenticator $authenticator
     */
    protected function setAuthenticator(AuthenticatorInterface $authenticator)
    {
        $this->authenticator = $authenticator;
    }

    /**
     * @return AbstractGuardAuthenticator
     */
    public function getAuthenticator(): AuthenticatorInterface
    {
        return $this->authenticator;
    }

    /**
     * @return Request
     */
    public function getRequest(): \Symfony\Component\HttpFoundation\Request
    {
        return $this->request;
    }

    /**
     * @param Request $request
     */
    public function setRequest(\Symfony\Component\HttpFoundation\Request $request)
    {
        $this->request = $request;
    }
}
