<?php

namespace App\Controller;

use App\Service\LoginService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    private $loginService;

    public function __construct( LoginService $loginService)
    {
        $this->loginService = $loginService;
    }

    /**
     * @Route("/login", name="login")
     * @param Request $request
     */
   public function login(Request $request)
   {

   }

    /**
     * @Route("/register", name="register", methods={"POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
   public function register(Request $request)
   {
       try {
           $data = json_decode($request->getContent(), TRUE);

           if(empty($data['userName']) ||
              empty($data['password']) ||
              empty($data['fullName']))
           {
               return $this->json(['status' => false,
                                   'message' => 'Please fill the required input',
                                   'data' => []],Response::HTTP_BAD_REQUEST);
           }

           $registerResult = $this->loginService->register($data);

           $statusCode = Response::HTTP_OK;

           if(!$registerResult['status'])
           {
               $statusCode = Response::HTTP_BAD_REQUEST;
           }

           return $this->json($registerResult,$statusCode);



       }catch (\Exception $exception) {
           return $this->json(['status' => false,
                               'message' => $exception->getMessage(),
                               'data' => []],Response::HTTP_BAD_REQUEST);

       }

   }
}
