<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use App\Form\OrderType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

class OrderController extends AbstractController
{
    public function __construct(private readonly ManagerRegistry $doctrine) {}
    #[Route('/', name: 'app_order')]
    public function index(): Response
    {
        return $this->render('base.html.twig', [
            'controller_name' => 'OrderController',
        ]);
    }

    #[Route('/new', name: 'app_order_new')]
    public function new(Request $request, EntityManagerInterface $entityManager){
        $order = new Order();
//        $rsm = new ResultSetMapping();
//        $products = $entityManager->createNativeQuery('select * from product ORDER BY id DESC LIMIT 10', $rsm)->getResult();;

        /** @var ProductRepository $repository */
        $repository = $this->doctrine->getRepository(Product::class);
        $options['products'] = $repository->getAllProducts();

        dump($options);

        $form = $this->createForm(OrderType::class, $order, $options);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Product $product */
            $product = $form->get('product')->getData();


            return $this->redirectToRoute('app_order', [
                'id' => $product->getId()
            ]);
        }

        return $this->render('order/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
