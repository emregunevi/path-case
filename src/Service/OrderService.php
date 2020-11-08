<?php


namespace App\Service;


use App\Entity\Order;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

class OrderService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function add($data)
    {
        try {
            /**
             * @var Product $product
             */
            $product = $this->entityManager->getRepository('App:Product')->findOneBy(['id' => $data['productId']]);

            if(empty($product))
            {
                return ['status' => false,
                        'message' => 'Product is not found',
                        'data' => []];
            }

            $now = new \DateTime();

            if(strtotime($now->format('Y-m-d')) >= strtotime($data['shippingDate']))
            {
                return ['status' => false,
                        'message' => 'The shipping date can not be before today',
                        'data' => []];
            }

            $orderCode = uniqid();

            $order = new Order();

            $order->setProduct($product);
            $order->setQuantity($data['quantity']);
            $order->setAddress($data['address']);
            $order->setShippingDate(new \DateTime($data['shippingDate']));
            $order->setUser($data['user']);
            $order->setOrderCode($orderCode);
            $order->setCreatedAt(new \DateTime());


            $this->entityManager->persist($order);
            $this->entityManager->flush();

            return ['status' => true,
                    'message' => 'Success',
                    'data' => $order];


        }catch (\Exception $exception) {
            return ['status' => false,
                    'message' => $exception->getMessage(),
                    'data' => []];
        }
    }

    public function edit($id,$data)
    {
        try {

            $order = $this->entityManager->getRepository('App:Order')->findOneBy(['id' => $id]);

            if(empty($order))
            {
                return ['status' => false,
                        'message' => 'Order not found. Please check the order Id',
                        'data' => []];
            }

            $now = new \DateTime();

            if(strtotime($now->format('Y-m-d')) >= strtotime($order->getShippingDate()->format('Y-m-d')))
            {
                return ['status' => false,
                        'message' => 'No updates can be made as the shipping date has passed',
                        'data' => []];
            }


            if(strtotime($now->format('Y-m-d')) >= strtotime($data['shippingDate']))
            {
                return ['status' => false,
                        'message' => 'The shipping date can not be before today',
                        'data' => []];
            }

            /**
             * @var Product $product
             */
            $product = $this->entityManager->getRepository('App:Product')->findOneBy(['id' => $data['productId']]);

            if(empty($product))
            {
                return ['status' => false,
                        'message' => 'Product is not found',
                        'data' => []];
            }

            if($data['userId'] != $order->getUser()->getId())
            {
                return ['status' => false,
                        'message' => 'This user is not authorized this order',
                        'data' => []];
            }

            $order->setProduct($product);
            $order->setQuantity($data['quantity']);
            $order->setAddress($data['address']);
            $order->setShippingDate(new \DateTime($data['shippingDate']));


            $this->entityManager->persist($order);
            $this->entityManager->flush();

            return ['status' => true,
                    'message' => 'Order update successfully',
                    'data' => $order];



        }catch (\Exception $exception) {
            return ['status' => false,
                    'message' => $exception->getMessage(),
                    'data' => []];
        }
    }

    public function get($id)
    {
        try {

            $order = $this->entityManager->getRepository('App:Order')->findOneBy(['id' => $id]);

            if(empty($order))
            {
                return ['status' => false,
                        'message' => 'Order not found. Please check the order Id',
                        'data' => []];
            }

            return ['status' => true,
                    'message' => 'Success',
                    'data' => $order];

        }catch (\Exception $exception)
        {
            return ['status' => false,
                    'message' => $exception->getMessage(),
                    'data' => []];

        }
    }

    public function getAll($user)
    {
        try {

            $orders = $this->entityManager->getRepository('App:Order')->findBy(['user' => $user]);


            if(empty($orders))
            {
                return ['status' => false,
                        'message' => 'Order not found for this user',
                        'data' => []];
            }

            return ['status' => true,
                    'message' => 'Success',
                    'data' => $orders];

        }catch (\Exception $exception)
        {
            return ['status' => false,
                'message' => $exception->getMessage(),
                'data' => []];
        }
    }
}