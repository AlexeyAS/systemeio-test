<?php

namespace App\Controller;

use App\Enum\ControllerEnum;
use App\Traits\CalculateTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Finder;

class CalculateController extends AbstractController
{
    use CalculateTrait;

    #[Route('/'.ControllerEnum::CALCULATE_NAME, name: ControllerEnum::CALCULATE_CONTROLLER, methods: 'GET')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $finder = new Finder($entityManager);
        $req = ($content = $request->getContent()) ? json_decode($content, true) : [];
        $productId = $req['product'] ?? null;
        $product = $finder->findByIdProduct($productId);
        $saleCode = $req['couponCode'] ?? null;
        $countryCode = $req['countryCode'] ?? null;
        if ($productId && $product && $countryCode) {
            $sale = $finder->getSale($saleCode);
            $tax = $finder->getTax($countryCode);
            $calculate = $this->calculatePrice($product->getPrice(), $sale, $tax);
        }
        if (isset($calculate['price']) && $calculate['price']){
            $response['success'] = true;
            $response['price'] = $calculate['price'];
            return new Response(json_encode($response), 200);
        } else {
            $response['success'] = false;
            $response['error_message'] = $calculate['error_message'] ?? 'Calculation Error';
            return new Response(json_encode($response), 400);
        }
    }
}
