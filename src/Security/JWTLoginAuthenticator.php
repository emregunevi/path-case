<?php

namespace App\Security;

use App\Service\JwtTokenService;
use Firebase\JWT\JWT;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class JWTLoginAuthenticator extends AbstractGuardAuthenticator
{
    private $jwtTokenService;

    public function __construct(JwtTokenService $jwtTokenService)
    {
        $this->jwtTokenService = $jwtTokenService;
    }

    public function supports(Request $request)
    {
        if(!$request->cookies->get("jwt") &&
            $request->attributes->get('_route') != 'login' &&
            $request->attributes->get('_route') != 'register')
        {
            throw new CustomUserMessageAuthenticationException('Jwt not found');
        }

        return $request->cookies->get("jwt") ? true : false;
    }

    public function getCredentials(Request $request)
    {
       $jwt = $request->cookies->get("jwt");

        try {

            $decodeJwtResult = $this->jwtTokenService->decodeJWT($jwt);

            if($decodeJwtResult['status'])
            {
                return ['userName' => $decodeJwtResult['data']['userName'],
                        'userId' => $decodeJwtResult['data']['userId']];

            } else {
                throw new CustomUserMessageAuthenticationException($decodeJwtResult['message']);
            }

        }catch (\Exception $exception) {
            throw new CustomUserMessageAuthenticationException($exception->getMessage());

        }

    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        return $userProvider->loadUserByUsername($credentials['userName']);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return $user->getUsername() == $credentials['userName'];
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new JsonResponse(['message' => $exception->getMessage()],403);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        //$userName = $token->getUser()->getUsername();
        return null;

    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return null;
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
