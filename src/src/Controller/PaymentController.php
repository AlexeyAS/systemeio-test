<?php

namespace App\Controller;

use App\Entity\Order;
use App\Enum\ControllerEnum;
use App\Enum\PaymentEnum;
use App\Traits\PaymentProcessorTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    use PaymentProcessorTrait;
    #[Route('/'.PaymentEnum::PAYMENT_NAME, name: ControllerEnum::PAYMENT_CONTROLLER, methods: 'POST')]
    public function index(Order $order): Response
    {
        $paymentProcessor = $order->getPaymentProcessor();
        $price = (int) $order->getPrice();
        $paymentProcessor && ($obj = $this->getPaymentProcessorObject($paymentProcessor));
        $paymentProcessor && ($method = $this->getPaymentProcessorMethod($paymentProcessor));
        isset($obj, $method) && ($result = $obj->{$method}($price));
        $response = $this->getPaymentProcessorResponse($result ?? null, $paymentProcessor);
        return new Response(json_encode($response));
    }
}
