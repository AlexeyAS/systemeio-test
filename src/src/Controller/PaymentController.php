<?php

namespace App\Controller;

use App\Enum\ControllerEnum;
use App\Enum\ErrorEnum;
use App\Enum\PaymentEnum;
use App\Service\Transformer;
use App\Traits\PaymentProcessorTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    use PaymentProcessorTrait;
    #[Route('/'.PaymentEnum::PAYMENT_NAME, name: ControllerEnum::PAYMENT_CONTROLLER, methods: 'POST')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $transformer = new Transformer($entityManager);
        $requestArray = ($content = $request->getContent()) ? json_decode($content, true) : [];
        $orderId = $requestArray['order_id'] ?? null;
        $orderId && $order = $transformer->findByIdOrder($orderId);

        if (isset($order) && $order) {
            $paymentProcessor = $order->getPaymentProcessor();
            $price = (int) $order->getPrice();
            $paymentProcessor && ($obj = $this->getPaymentProcessorObject($paymentProcessor));
            $paymentProcessor && ($method = $this->getPaymentProcessorMethod($paymentProcessor));
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
