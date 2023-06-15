<?php

namespace App\Controller;

use App\Enum\ControllerEnum;
use App\Enum\ErrorEnum;
use App\Traits\PaymentProcessorTrait;
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
    public function index(Request $request): Response
    {
        $req = ($content = $request->getContent()) ? json_decode($content, true) : [];
        if (isset($req['product'], $req['taxNumber'], $req['couponCode'], $req['paymentProcessor'], $req['price'])) {
            $paymentProcessor = $req['paymentProcessor'];
            $price = (int) $req['price'];
            $paymentProcessor && ($obj = $this->paymentProcessorFactory->createObject($paymentProcessor));
            $paymentProcessor && ($method = $this->paymentProcessorFactory->getMethod($paymentProcessor));
            isset($obj, $method) && ($result = $obj->{$method}($price));
            $response = $this->getPaymentProcessorResponse($result ?? null, $paymentProcessor);
            if ($response['success']) {
                return new Response(json_encode($response), 200);
            } else {
                return new Response(json_encode($response), 400);
            }
        } else {
            $response['success'] = false;
            $response['error_message'] = ErrorEnum::ERROR_LOST_ORDER;
            return new Response(json_encode($response), 400);
        }
    }
}
