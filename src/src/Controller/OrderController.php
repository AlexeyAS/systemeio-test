<?php

namespace App\Controller;

use App\Entity\Order;
use App\Enum\OrderEnum;
use App\Enum\ControllerEnum;
use App\Entity\Product;
use App\Enum\PaymentEnum;
use App\Form\OrderType;
use App\Repository\ProductRepository;
use App\Repository\SaleRepository;
use App\Repository\TaxRepository;
use App\Service\Transformer;
use App\Traits\PaymentProcessorTrait;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Traits\CalculateTrait;
use Exception;

class OrderController extends AbstractController
{
    use CalculateTrait;
    use PaymentProcessorTrait;
    public function __construct(private readonly ManagerRegistry $doctrine) {}

    /**
     * @throws NonUniqueResultException
     */
    #[Route('/', name: ControllerEnum::ORDER_CONTROLLER, methods: ['GET','POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $order = new Order();
        $transformer = new Transformer($entityManager);
        $options['products'] = $transformer->getAllProducts();

//        $calcData = [];
//        $request->get('req') && parse_str($request->get('req'), $calcData);
//
//        $productId = $calcData['product_id'] ?? null;
//        $paymentProcessor = $calcData['payment_processor'] ?? null;
//        $saleId = $calcData['sale_id'] ?? null;
//        $saleCode = $calcData['sale_code'] ?? null;
//        $countryCode = $calcData['country_code'] ?? null;
//        $taxNumber = $calcData['tax_number'] ?? null;
//        $price = $calcData['price'] ?? $request->get('price') ?? null;
//        if ($calcData) {
//            $order->setProduct($productId);
//            $order->setPaymentProcessor($paymentProcessor);
//            $order->setCountryCode($countryCode);
//            $order->setSaleCode($saleCode);
//            $order->setTaxNumber($taxNumber);
//            $order->setPrice($price);
//        }


        $form = $this->createForm(OrderType::class, $order, $options);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() && method_exists($form, 'getClickedButton')) {
            $productId = $form->get('product')->getData();
            $countryCode = $form->get('country_code')->getData() ?? null;
            $saleCode = $form->get('sale_code')->getData();
            $taxNumber = $form->get('tax_number')->getData();
            $paymentProcessor = $form->get('payment_processor')->getData() ?? null;
            $price = $form->get('price')->getData() ?? null;
            $action = $form->getClickedButton()->getName() ?: OrderEnum::ORDER_ACTION_CALCULATE;

            $product = $transformer->findByIdProduct($productId);
            $sale = $transformer->getSale($saleCode);
            $tax = $transformer->getTax($countryCode);
            $price = $this->calculatePrice($product->getPrice(), $sale, $tax);

            if ($action === OrderEnum::ORDER_ACTION_CALCULATE) {
                return $this->redirectToRoute(ControllerEnum::CALCULATE_CONTROLLER, [
                    'req' => $transformer->calcRequestQuery(
                        $productId,
                        $sale ? ($sale->getId()) : null,
                        $tax ? ($tax->getId()) : null,
                        $price ?: null,
                        $taxNumber ?: null,
                        $countryCode ?: null,
                        $saleCode ?: null,
                        $paymentProcessor ?: null
                    )
                ]);
            }
            elseif($action === OrderEnum::ORDER_ACTION_PAYMENT && $request->isMethod('POST')) {
                $productId && $order->setProduct($productId);
                $saleCode && $order->setSaleCode($saleCode);
                $paymentProcessor && $order->setPaymentProcessor($paymentProcessor);
                $countryCode && $order->setCountryCode($countryCode);
                $price && $order->setPrice($price);
                $taxNumber && $order->setTaxNumber($taxNumber);
                $entityManager->persist($order);
                $entityManager->flush();

                //@deprecated
                $paymentProcessor && ($obj = $this->getPaymentProcessorObject($paymentProcessor));
                $paymentProcessor && ($method = $this->getPaymentProcessorMethod($paymentProcessor));
                /**
                 * @throws Exception
                 */
                $result = '';
                isset($obj,$method) && ($result = $obj->{$method}($price));
                is_array($result) && ($result = json_encode($result));

                /**
                 * TODO PaymentController
                 */
                return $this->render('payment/payment.html.twig', [
                    'order' => $order,
                    'result' => $result
                ]);
            }
        }
        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'products' => $options['products']
        ]);
    }
}
