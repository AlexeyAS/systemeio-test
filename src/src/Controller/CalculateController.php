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
        $response = new Response();
        $finder = new Finder($entityManager);
        $requestArray = ($content = $request->getContent()) ? json_decode($content, true) : [];

        $productId = $requestArray['product_id'] ?? null;
        $saleCode = $requestArray['sale_code'] ?? null;
        $countryCode = $requestArray['country_code'] ?? null;

        $product = $finder->findByIdProduct($productId);
        $sale = $finder->getSale($saleCode);
        $tax = $finder->getTax($countryCode);
        $price = $this->calculatePrice($product->getPrice(), $sale, $tax);

        return $response->setContent(json_encode(['price'=>$price]));
    }
}
