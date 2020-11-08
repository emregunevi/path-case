<?php


namespace App\Service;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Firebase\JWT\JWT;

class JwtTokenService
{

    private $jwtTokenKey;
    private $entityManager;
    private $userService;

    public function __construct(UserService $userService,EntityManagerInterface $entityManager)
    {
        $this->jwtTokenKey = getenv("JWT_TOKEN_KEY");
        $this->userService = $userService;
        $this->entityManager = $entityManager;

    }

    public function generateJwtToken($userId,$userName)
    {
        try {
            $expireJWTDate = time() + (2 * 60 * 60);

            $payload = ["userId" => $userId,
                        "userName" => $userName,
                        'iat' => time(),
                        "exp" => $expireJWTDate,

                    ];


            $jwt = JWT::encode($payload,$this->jwtTokenKey);

            setcookie('jwt',$jwt,$expireJWTDate,'/',"",false, false);

            return [
                    'status' => true,
                    'message' => 'Success',
                    'data' => $jwt
            ];

        }catch (\Exception $exception) {

            return ['status' => false,
                    'message' => $exception->getMessage(),
                    'data' => []];
        }

    }

    /**
     * @param string $jwt
     * @return array
     */

    public function decodeJWT(string $jwt)
    {
        try {

            $decoded = JWT::decode($jwt,$this->jwtTokenKey,['HS256']);

            $data = (array) $decoded;

            return ['status' => true,
                'message' => 'Success',
                'data' => $data];

        }catch (\Exception $exception) {

            return ['status' => false,
                'message' => $exception->getMessage(),
                'data' => []];
        }
    }

}