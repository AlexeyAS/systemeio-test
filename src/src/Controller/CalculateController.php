<?php

namespace App\Controller;

use App\Entity\Order;
use App\Enum\ControllerEnum;
use App\Enum\OrderEnum;
use App\Form\OrderType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Transformer;

class CalculateController extends AbstractController
{
    //TODO DRY Calculate Order
    #[Route('/calculate', name: 'app_calculate', methods: 'GET')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $order = new Order();
        $transformer = new Transformer($entityManager);
        $calcRequestQuery = $request->query->get('req');
        $calcRequestArray = $transformer->calcRequestArray($calcRequestQuery) ?: [];
        $options['products'] = $transformer->getAllProducts();
        $price = $calcRequestArray['price'] ?? $request->get('price') ?? null;

        if ($calcRequestArray) {
            ($calcRequestArray['product_id'] ?? null) && $order->setProduct($calcRequestArray['product_id']);
            ($calcRequestArray['payment_processor'] ?? null) && $order->setPaymentProcessor($calcRequestArray['payment_processor']);
            ($calcRequestArray['country_code'] ?? null) && $order->setCountryCode($calcRequestArray['country_code']);
            ($calcRequestArray['sale_code'] ?? null) && $order->setSaleCode($calcRequestArray['sale_code']);
            ($calcRequestArray['tax_number'] ?? null) && $order->setTaxNumber($calcRequestArray['tax_number']);
            $price && $order->setPrice($price);
        }
        $form = $this->createForm(OrderType::class, $order, $options);
        $form->handleRequest($request);

//        if ($form->isSubmitted() && $form->isValid() && method_exists($form, 'getClickedButton')) {
//            $productId = $form->get('product')->getData();
//            $countryCode = $form->get('country_code')->getData() ?? null;
//            $saleCode = $form->get('sale_code')->getData();
//            $taxNumber = $form->get('tax_number')->getData();
//            $paymentProcessor = $form->get('payment_processor')->getData() ?? null;
//            $price = $form->get('price')->getData() ?? null;
//            $action = $form->getClickedButton()->getName() ?: OrderEnum::ORDER_ACTION_CALCULATE;
//
//            if ($action === OrderEnum::ORDER_ACTION_CALCULATE) {
                return $this->redirectToRoute(ControllerEnum::CALCULATE_CONTROLLER, [
                    'req' => $transformer->calcRequestQuery(
                        $calcRequestArray['product_id'] ?? null,
                        $calcRequestArray['sale_id'] ?? null,
                        $calcRequestArray['tax_id'] ?? null,
                        $price ?: null,
                        $calcRequestArray['tax_number'] ?? null,
                        $calcRequestArray['country_code'] ?? null,
                        $calcRequestArray['sale_code'] ?? null,
                        $calcRequestArray['payment_processor'] ?? null
                    )
                ]);
//            }
//        }
//        return $this->render('order/index.html.twig', [
//            'form' => $form->createView(),
//            'products' => $options['products'],
//            'price' => $price
//        ]);
    }
}
