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
     * @param string|array $get
     * @return array
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function calculate(string|array $get): array
    {
        $response = [];
        $url = 'http://nginx/' . ControllerEnum::CALCULATE_NAME;
//        $client = $this->client->withOptions(
//            [
//                'base_uri' => [
//                'http://localhost/'
//            ],
//                'http://127.0.0.1/'
//            ]
//        );
        $request = $this->client->request('GET', $url, ['json' => $get]);
        $request->toArray() && $response = $request->toArray();
        !$response && $response = ['status_code' => $request->getStatusCode(), 'success' => false];
        return $response;
    }

    /**
     * Endpoint: /payment POST
     * @param Order $order
     * @param EntityManagerInterface $entityManager
     * @param bool $removeFailedOrder
     * Remove order if payment not success
     * @return array
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function payment(Order $order, EntityManagerInterface $entityManager, bool $removeFailedOrder = false): array
    {
        $url = 'http://nginx/' . ControllerEnum::PAYMENT_NAME;
        $productId = $order->getProduct();
        $product = $entityManager->getRepository(Product::class)->findOneBy(['id'=>$productId]);
        $order->setProduct($product);
        //save order
        $entityManager->persist($order);
        $entityManager->flush();

        $request = $this->client->request('POST', $url, ['json' => ['order_id' => $order->getId()]]);
        $request->toArray() && $response = $request->toArray();
        if (!isset($response) || !$response) {
            $response = ['status_code' => $request->getStatusCode(), 'success' => false];
            //delete order
            if ($removeFailedOrder){
                $entityManager->remove($order);
                $entityManager->flush();
            }
        }
        return $response;
    }
}