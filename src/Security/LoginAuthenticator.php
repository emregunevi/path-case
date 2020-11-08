<?php

namespace App\Security;

use App\Repository\UserRepository;
use App\Service\JwtTokenService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class LoginAuthenticator extends AbstractGuardAuthenticator
{
    private $jwtTokenService;
    private $userRepository;
    private $passwordEncoder;

    public function __construct(JwtTokenService $jwtTokenService, UserRepository $userRepository,UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->jwtTokenService = $jwtTokenService;
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function supports(Request $request)
    {

        return $request->attributes->get('_route') === "login" && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        $data = json_decode($request->getContent(), TRUE);

        $credentials= ["userName" => $data['userName'],
                       "password" => $data['password']];

        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $user = $this->userRepository->findOneBy(['userName' => $credentials['userName']]);

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        $check = $this->passwordEncoder->isPasswordValid($user, $credentials['password']);

        return $check;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new JsonResponse(['message' => $exception->getMessageData()], Response::HTTP_FORBIDDEN);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $jwtResult = $this->jwtTokenService->generateJwtToken($token->getUser()->getId(),$token->getUser()->getUserName());


        return new JsonResponse(['status' => $jwtResult['status']]);
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {

        $data = [

            'message' => 'Authentication Required'
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe()
    {
        // todo
    }
}
