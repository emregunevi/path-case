<?php


namespace App\Service;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class LoginService
{
    private $entityManager;
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function register($data)
    {
        try {

            $user = new User();
            $user->setUserName($data['userName']);
            $user->setPassword($data['password']);
            $user->setFullName($data['fullName']);
            $user->setCreatedAt(new \DateTime());

            $encodedPassword = $this->passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($encodedPassword);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

             return ['status' => true,
                     'message' => 'User add Successfully',
                     'data' => $user];

        }catch (\Exception $exception)
        {
            return ['status' => false,
                    'message' => $exception->getMessage(),
                    'data' => []];
        }
    }

}