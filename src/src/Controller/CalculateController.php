<?php

namespace App\Controller;

use App\Entity\Order;
use App\Enum\ControllerEnum;
use App\Enum\OrderEnum;
use App\Form\OrderType;
use App\Traits\CalculateTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Transformer;

class CalculateController extends AbstractController
{
    use CalculateTrait;

    #[Route('/calculate', name: 'app_calculate', methods: 'GET')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $response = new Response();
        $transformer = new Transformer($entityManager);
        $requestArray = ($content = $request->getContent()) ? json_decode($content, true) : [];

        $productId = $requestArray['product_id'] ?? null;
        $saleCode = $requestArray['sale_code'] ?? null;
        $countryCode = $requestArray['country_code'] ?? null;

        $product = $transformer->findByIdProduct($productId);
        $sale = $transformer->getSale($saleCode);
        $tax = $transformer->getTax($countryCode);
        $price = $this->calculatePrice($product->getPrice(), $sale, $tax);

        return $response->setContent(json_encode(['price'=>$price]));
    }
}
