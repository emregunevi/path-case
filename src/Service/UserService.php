<?php


namespace App\Service;


use Doctrine\ORM\EntityManagerInterface;

class UserService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function checkUserPassword($userName, $password)
    {
        try {

            if(empty($email) ||
                empty($password))
            {
                return ['status' => false,
                        'message' => 'Email or password not found',
                        'data' => []];
            }

            $user = $this->entityManager->getRepository('App:User')->findBy(['userName'=> $userName]);

            if(empty($user))
            {
                return ['status' => false,
                        'message' => 'Email or password not found',
                        'data' => []];

            }

            $userPassword = $user->getPassword();

            if($userPassword != $password)
            {
                return ['status' => false,
                        'message' => 'Invalid Password',
                        'data' => []];
            }

            return ['status' => true,
                    'message' => 'Invalid Password',
                    'data' => $user];

        }catch (\Exception $exception) {

            return ['status' => false,
                    'message' => $exception->getMessage(),
                    'data' => []];
        }
    }

}