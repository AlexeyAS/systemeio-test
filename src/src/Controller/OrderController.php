<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use App\Form\OrderType;
use App\Repository\ProductRepository;
use App\Repository\SaleRepository;
use App\Repository\TaxRepository;
use App\Service\Transformer;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Traits\CalculateTrait;

class OrderController extends AbstractController
{
    use CalculateTrait;
    public function __construct(private readonly ManagerRegistry $doctrine) {}
    #[Route('/', name: 'app_order')]
    public function index(): Response
    {
        return $this->render('base.html.twig', [
            'controller_name' => 'OrderController',
        ]);
    }

    /**
     * @throws NonUniqueResultException
     */
    #[Route('/new', name: 'app_order_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $order = new Order();
        $transformer = new Transformer($entityManager);

        if ($request->get('price')) {
            $order->setPrice($request->get('price'));
        }

        $order->setCountryCode($request->get('country_code') ?? null);
        $order->setSaleCode($request->get('sale_code') ?? '');
        $order->setPaymentProcessor($request->get('payment_processor') ?? null);
        $productId = $request->get('order')['product'] ?? null;
        if ((int) $productId) {
            $order->setProduct((int) $productId);
        }

        $options['products'] = $transformer->getAllProducts();
        $form = $this->createForm(OrderType::class, $order, $options);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productId = $form->get('product')->getData();
            dump($productId);
            $product = $transformer->findByIdProduct($productId);
            $price = $product->getPrice();
            $sale = $transformer->checkSale($form->get('sale_code')->getData());
            if ($sale) {
                $price = $this->calculateSale($price, $sale);
            }
            $tax = $transformer->getTax($form->get('country_code')->getData());
            if ($tax) {
                $price = $this->calculateTax($price, $tax);
            }

            return $this->render('order/index.html.twig', [
                'form' => $form->createView(),
                'products' => $options['products'],
                'price' => $price
            ]);
        }

        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'products' => $options['products'],
        ]);
    }
}