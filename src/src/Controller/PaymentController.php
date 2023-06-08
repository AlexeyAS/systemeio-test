<?php

namespace App\Controller;

use App\Enum\ControllerEnum;
use App\Enum\PaymentEnum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    #[Route('/'.PaymentEnum::PAYMENT_NAME, name: ControllerEnum::PAYMENT_CONTROLLER, methods: 'POST')]
    public function index(): Response
    {
        return $this->render('payment/index.html.twig', [
            'controller_name' => 'PaymentController',
        ]);
    }
}
