<?php

namespace App\Service;

use App\Entity\Product;
use App\Enum\ControllerEnum;
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

class RequestService extends Finder
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
     * Endpoint: /calculate GET
     * @param Order $order
     * @param EntityManagerInterface $entityManager
     * @return array|null
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function calculate(Order $order, EntityManagerInterface $entityManager): ?array
    {
        $productId = $order->getProduct();
        $product = $entityManager->getRepository(Product::class)->findOneBy(['id'=>$productId]);
        $order->setProduct($product);
        $url = 'http://nginx/' . ControllerEnum::CALCULATE_NAME;
//        $client = $this->client->withOptions(
//            [
//                'base_uri' => [
//                'http://localhost/'
//            ],
//                'http://127.0.0.1/'
//            ]
//        );
        $request = $this->client->request('GET', $url, ['json' => $this->getRequestData($order)]);
        if ($request->getStatusCode() === 200) {
            $response = $request->toArray();
        } elseif ($request->getStatusCode() === 400) {
            $response = $request->toArray(false);
        }
        return $response ?? null;
    }

    /**
     * Endpoint: /payment POST
     * @param Order $order
     * @param EntityManagerInterface $entityManager
     * @return array|null
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function payment(Order $order, EntityManagerInterface $entityManager): ?array
    {
        $url = 'http://nginx/' . ControllerEnum::PAYMENT_NAME;
        $productId = $order->getProduct();
        $product = $entityManager->getRepository(Product::class)->findOneBy(['id'=>$productId]);
        $order->setProduct($product);

        $request = $this->client->request('POST', $url, ['json' => $this->getRequestData($order)]);
        if ($request->getStatusCode() === 200) {
            //save order
            $entityManager->persist($order);
            $entityManager->flush();
            $response = $request->toArray();
            $response['order_id'] = $order->getId();
        } elseif ($request->getStatusCode() === 400) {
            $response = $request->toArray(false);
        }
        return $response ?? null;
    }

    public function getRequestData(Order $order): array
    {
        $response = [
            'product' => $order->getProduct()->getId(),
            'taxNumber' => $order->getTaxNumber(),
            'couponCode' => $order->getSaleCode(),
            'paymentProcessor' => $order->getPaymentProcessor(),
            'countryCode' => $order->getCountryCode()
        ];
        $order->getPrice() && $response['price'] = $order->getPrice();
        return $response;
    }
}