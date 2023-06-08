<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RequestService extends Transformer
{
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

        $response = $this->client->request('GET', 'http://nginx/calculate',
            [
//            'base_uri' => [
//                'http://localhost/'
//            ],
                'json' => $get
            ]);
        $content = $response->toArray();

        $response->toArray() && $responseArray = $response->toArray();
        !$responseArray && $responseArray = ['status_code' => $response->getStatusCode(), 'success' => false];
        return $content;
    }
}