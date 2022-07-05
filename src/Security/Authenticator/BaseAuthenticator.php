<?php

namespace ByTIC\Auth\Security\Authenticator;

use ByTIC\Auth\AuthServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\PasswordUpgradeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

/**
 * Class BaseAuthenticator
 * @package ByTIC\Auth\Security\Guard\Authenticator
 */
class BaseAuthenticator extends AbstractAuthenticator implements AuthenticationEntryPointInterface
{

    /**
     * @var UserProviderInterface
     */
    protected UserProviderInterface $userProvider;

    public function __construct(UserProviderInterface $userProvider = null)
    {
        $this->userProvider = $userProvider ?? app(AuthServiceProvider::USER_PROVIDER);
    }

    public function authenticate(Request $request): Passport
    {
        $credentials = $this->getCredentials($request);
        $method = 'loadUserByIdentifier';

        if (!method_exists($this->userProvider, 'loadUserByIdentifier')) {
            trigger_deprecation(
                'symfony/security-core',
                '5.3',
                'Not implementing method "loadUserByIdentifier()" in user provider "%s" is deprecated. This method will replace "loadUserByUsername()" in Symfony 6.0.',
                get_debug_type($this->userProvider)
            );

            $method = 'loadUserByUsername';
        }

        $passport = new Passport(
            new UserBadge($credentials['username'], [$this->userProvider, $method]),
            new PasswordCredentials($credentials['password']),
            [new RememberMeBadge()]
        );
//        if ($this->options['enable_csrf']) {
//            $passport->addBadge(new CsrfTokenBadge($this->options['csrf_token_id'], $credentials['csrf_token']));
//        }

        if ($this->userProvider instanceof PasswordUpgraderInterface) {
            $passport->addBadge(new PasswordUpgradeBadge($credentials['password'], $this->userProvider));
        }

        return $passport;
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
    public function getCredentials(Request $request): array
    {
        return [
            'username' => $request->request->get('_username'),
            'password' => $request->request->get('_password'),
        ];
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getPassword($credentials): ?string
    {
        return $credentials['password'];
    }

    /**
     * @inheritDoc
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
    }

    /**
     * @inheritDoc
     */
    public function createToken(Passport $passport, string $firewallName): TokenInterface
    {
        return new UsernamePasswordToken($passport->getUser(), $firewallName, $passport->getUser()->getRoles());
    }
}
