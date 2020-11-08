<?php

namespace App\Controller;

use App\Entity\Order;
use App\Service\OrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/order", name="order")
 */
class OrderController extends AbstractController
{
    /**
     * @Route("/add", name="order-add", methods={"POST"})
     * @param Request $request
     * @param OrderService $orderService
     * @return JsonResponse
     */
    public function add(Request $request, OrderService $orderService)
    {
        try {

            $data = json_decode($request->getContent(), TRUE);

            if(empty($data['productId']) ||
               empty($data['quantity']) ||
               empty($data['address']) ||
               empty($data['shippingDate']))
            {
                return $this->json(['status' => false,
                                    'message' => 'Product, quantity, address or shipping date can not be empty',
                                    'data' => []],Response::HTTP_BAD_REQUEST);
            }

            if($data['quantity'] < 0 )
            {
                return $this->json(['status' => false,
                                    'message' => 'Quantity cannot be less than zero',
                                    'data' => []]);
            }

            $data['user'] = $this->getUser();

            $orderResult = $orderService->add($data);

            $statusCode = Response::HTTP_OK;

            if(!$orderResult['status'])
            {
                $statusCode = Response::HTTP_BAD_REQUEST;
            }

            return $this->json($orderResult, $statusCode);



        }catch (\Exception $exception) {

            return $this->json(['status' => false,
                                'message' => $exception->getMessage(),
                                'data' => []],Response::HTTP_BAD_REQUEST);
        }

    }


    /**
     * @Route("/edit/{id}", name="orde-edit", methods={"POST"})
     * @param $id
     * @param OrderService $orderService
     * @param Request $request
     * @return JsonResponse
     */
    public function edit($id, Request $request, OrderService $orderService)
    {
        try {

            $data = json_decode($request->getContent(), TRUE);

            if(empty($data['productId']) ||
                empty($data['quantity']) ||
                empty($data['address']) ||
                empty($data['shippingDate']))
            {
                return $this->json(['status' => false,
                                    'message' => 'Product, quantity, address or shipping date can not be empty',
                                    'data' => []]);
            }

            if($data['quantity'] < 0 )
            {
                return $this->json(['status' => false,
                                    'message' => 'Quantity cannot be less than zero',
                                    'data' => []]);
            }

            $user = $this->getUser();

            $data['userId'] = $user->getId();

            $editResult = $orderService->edit($id,$data);

            $statusCode = Response::HTTP_OK;

            if(!$editResult['status'])
            {
                $statusCode = Response::HTTP_BAD_REQUEST;
            }

            return $this->json($editResult, $statusCode);

        }catch (\Exception $exception) {
            return $this->json(['status' => false,
                                'message' => $exception->getMessage(),
                                'data' => []],Response::HTTP_BAD_REQUEST);
        }

    }

    /**
     * @Route("/get/{id}", name="order-get", methods={"GET"})
     * @param $id
     * @param OrderService $orderService
     * @return JsonResponse
     */
    public function getOne($id, OrderService $orderService)
    {
        try {

            $orderResult = $orderService->get($id);

            if(!$orderResult['status'])
            {
                return $this->json($orderResult,Response::HTTP_BAD_REQUEST);
            }

            $order = $orderResult['data'];

            $user = $this->getUser();

            if($order->getUser()->getId() != $user->getId())
            {
                return $this->json(['status' => false,
                                    'message' => 'This user is not authorized this order',
                                    'data' => []],Response::HTTP_BAD_REQUEST);
            }

            return $this->json(['status' => true,
                                'message' => 'Success',
                                'data' => $order]);

        }catch (\Exception $exception) {
            return $this->json(['status' => false,
                                'message' => $exception->getMessage(),
                                'data' => []],Response::HTTP_BAD_REQUEST);
        }

    }

    /**
     * @Route("/get-all", name="order-get-all", methods={"GET"})
     * @param OrderService $orderService
     * @return JsonResponse
     */
    public function getAll(OrderService $orderService)
    {
        try {

            $user = $this->getUser();

            $ordersData = $orderService->getAll($user);

            $status = Response::HTTP_OK;
            if(!$ordersData['status'])
            {
                $status = Response::HTTP_BAD_REQUEST;
            }

            return $this->json($ordersData,$status);

        }catch (\Exception $exception) {
            return $this->json(['status' => false,
                                'message' => $exception->getMessage(),
                                'data' => []],Response::HTTP_BAD_REQUEST);
        }

    }
}
