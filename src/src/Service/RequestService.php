<?php

namespace App\Service;

use App\Entity\Product;
use App\Enum\CalculateEnum;
use App\Traits\PaymentProcessorTrait;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Entity\Order;

class RequestService extends Transformer
{
    use PaymentProcessorTrait;
    private EntityManagerInterface $em;

    public function __construct(private HttpClientInterface $client, EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
        $this->em = $entityManager;
        $this->client = $client;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws DecodingExceptionInterface
     */
    public function calculate(string|array $get): array
    {
        $responseArray = [];
//        $client = $this->client->withOptions(
//            [
//                'base_uri' => [
//                'http://localhost/'
//            ],
//                'http://127.0.0.1/'
//            ]
//        );
        $response = $this->client->request('GET', 'http://nginx/' . CalculateEnum::CALCULATE_NAME, ['json' => $get]);
        $response->toArray() && $responseArray = $response->toArray();
        !$responseArray && $responseArray = ['status_code' => $response->getStatusCode(), 'success' => false];
        return $responseArray;
    }

    public function payout(Order $order, EntityManagerInterface $entityManager):array {
        $responseArray = [];
        $productId = $order->getProduct();
        $product = $entityManager->getRepository(Product::class)->findOneBy(['id'=>$productId]);
        $order->setProduct($product);

        //save order
        $entityManager->persist($order);
        $entityManager->flush();

        $order->getPaymentProcessor() && ($obj = $this->getPaymentProcessorObject($order->getPaymentProcessor()));
        $order->getPaymentProcessor() && ($method = $this->getPaymentProcessorMethod($order->getPaymentProcessor()));

        /**
         * @throws Exception
         */
        $result = '';
        isset($obj, $method) && ($result = $obj->{$method}((int) $order->getPrice()));
        $result && $responseArray['status'] = json_encode($result);
        $responseArray['success'] = true;
        return $responseArray;
    }
}