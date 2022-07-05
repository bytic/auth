<?php

namespace ByTIC\Auth\Security\Authenticator;

use ByTIC\Auth\Models\Users\Resolvers\UsersResolvers;
use ByTIC\Auth\Services\JWTManager;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\InvalidTokenException;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\ChainTokenExtractor;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\TokenExtractorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

/**
 * Class JwtAuthenticator
 * @package ByTIC\Auth\Security\Guard\Authenticator
 */
class JwtAuthenticator extends AbstractAuthenticator implements AuthenticationEntryPointInterface
{
    /**
     * @var TokenExtractorInterface
     */
    protected $tokenExtractor;

    /**
     * @var JWTManager
     */
    protected $jwtManager;

    /**
     * JwtAuthenticator constructor.
     * @param TokenExtractorInterface $tokenExtractor
     */
    public function __construct()
    {
        $this->tokenExtractor = new ChainTokenExtractor(
            [
                new AuthorizationHeaderTokenExtractor('Bearer', 'Authorization'),
                new AuthorizationHeaderTokenExtractor('Token', 'Authorization')
            ]
        );

        if (function_exists('app') && app()->has('auth')) {
            $jwtManager = auth()->jwtManager();
        } else {
            $jwtManager = new JWTManager();
        }
        $this->jwtManager = $jwtManager;
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
    public function supports(Request $request): ?bool
    {
        return false !== $this->getTokenExtractor()->extract($request);
    }

    /**
     * @inheritDoc
     */
    public function authenticate(Request $request): Passport
    {
        $token = $this->getTokenExtractor()->extract($request);

        try {
            if (!$payload = $this->jwtManager->parse($token)) {
                throw new InvalidTokenException('Invalid JWT Token');
            }
        } catch (\Exception $e) {
            throw new InvalidTokenException('Invalid JWT Token', 0, $e);
        }

        $claims = $payload->claims();
        $userIdentifier = $claims->get('sub');

        $passport = new SelfValidatingPassport(
            new UserBadge(
                $userIdentifier,
                function ($userIdentifier) {
                    return UsersResolvers::resolve($userIdentifier);
                }
            )
        );

        $passport->setAttribute('jwt', $payload);

        return $passport;
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return null;
    }

    /**
     * Gets the token extractor to be used for retrieving a JWT token in the
     * current request.
     *
     * Override this method for adding/removing extractors to the chain one or
     * returning a different {@link TokenExtractorInterface} implementation.
     */
    protected function getTokenExtractor(): TokenExtractorInterface
    {
        return $this->tokenExtractor;
    }
}