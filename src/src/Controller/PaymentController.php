<?php

namespace App\Controller;

use App\Enum\ControllerEnum;
use App\Enum\ErrorEnum;
use App\Service\Finder;
use App\Traits\PaymentProcessorTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Factory\PaymentProcessorFactory;

class PaymentController extends AbstractController
{
    use PaymentProcessorTrait;

    private PaymentProcessorFactory $paymentProcessorFactory;

    public function __construct(){
        $this->paymentProcessorFactory = new PaymentProcessorFactory();
    }

    #[Route('/'.ControllerEnum::PAYMENT_NAME, name: ControllerEnum::PAYMENT_CONTROLLER, methods: 'POST')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $finder = new Finder($entityManager);
        $requestArray = ($content = $request->getContent()) ? json_decode($content, true) : [];
        $orderId = $requestArray['order_id'] ?? null;
        $orderId && $order = $finder->findByIdOrder($orderId);

        if (isset($order) && $order) {
            $paymentProcessor = $order->getPaymentProcessor();
            $price = (int) $order->getPrice();
            $paymentProcessor && ($obj = $this->paymentProcessorFactory->createObject($paymentProcessor));
            $paymentProcessor && ($method = $this->paymentProcessorFactory->getMethod($paymentProcessor));
            isset($obj, $method) && ($result = $obj->{$method}($price));
            $response = $this->getPaymentProcessorResponse($result ?? null, $paymentProcessor);
            $response['order_id'] = $orderId;
        } else {
            $response['success'] = false;
            $response['error_message'] = ErrorEnum::ERROR_LOST_ORDER;
        }
        return new Response(json_encode($response));
    }
}
