<?php

namespace App\Controller;

use App\Entity\Order;
use App\Enum\ControllerEnum;
use App\Entity\Product;
use App\Form\OrderFormType;
use App\Service\RequestService;
use App\Traits\PaymentProcessorTrait;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Traits\CalculateTrait;
use Exception;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OrderController extends AbstractController
{
    use CalculateTrait;
    use PaymentProcessorTrait;
    public function __construct(private readonly ManagerRegistry $doctrine) {}

    /**
     * @throws NonUniqueResultException
     * @throws ClientExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws TransportExceptionInterface
     * @throws DecodingExceptionInterface
     */
    #[Route('/', name: ControllerEnum::ORDER_CONTROLLER, methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $entityManager, HttpClientInterface $client): Response
    {
        $order = new Order();
        //TODO ServiceInterface
        $service = new RequestService($client, $entityManager);
        $options['products'] = $service->getAllProducts();

        $form = $this->createForm(OrderFormType::class, $order, $options);
        $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid() && method_exists($form, 'getClickedButton')) {
                $action = $form->getClickedButton()->getName() ?: ControllerEnum::CALCULATE_NAME;
                $productId = $form->get('product')->getData();
                $saleCode = $form->get('sale_code')->getData();
                $countryCode = $form->get('country_code')->getData() ?? null;
                $taxNumber = $form->get('tax_number')->getData();
                $paymentProcessor = $form->get('payment_processor')->getData() ?? null;
                $price = $form->get('price')->getData() ?? null;

                if ($action === ControllerEnum::CALCULATE_NAME || $action === ControllerEnum::PAYMENT_NAME) {
                    $calculateResponse = $service->calculate([
                        'product_id' => $productId,
                        'tax_number' => $taxNumber ?: null,
                        'country_code' => $countryCode ?: null,
                        'sale_code' => $saleCode ?: null,
                        'payment_processor' => $paymentProcessor ?: null
                    ]);
                    isset($calculateResponse['price']) && $price = $calculateResponse['price'];
                }
                if ($action === ControllerEnum::PAYMENT_NAME && $request->isMethod('POST')) {
                    if ($productId) {
                        $product = $entityManager->getRepository(Product::class)->findOneBy(['id' => $productId]);
                    }
                    isset($product) && $order->setProduct($product);
                    $saleCode && $order->setSaleCode($saleCode);
                    $paymentProcessor && $order->setPaymentProcessor($paymentProcessor);
                    $countryCode && $order->setCountryCode($countryCode);
                    $price && $order->setPrice($price);
                    $taxNumber && $order->setTaxNumber($taxNumber);

                    $response = $service->payment($order, $entityManager, true);
                    if (isset($response['success']) && $response['success']) {
                        $render['controller_name'] = 'PaymentSuccess';
                        $render['order_info'] = (isset($response['order_id']) && $response['order_id']) ?
                            $service->findByIdOrder($response['order_id']) : null;
                        return $this->render('payment/index.html.twig', $render);
                    } else {
                        $errorMessage = $response['error_message'] ?? 'Unknown Error';
                    }
                }
            }


        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'products' => $options['products'],
            'price' => $price ?? null,
            'action' => $action ?? null,
            'error_message' => $errorMessage ?? null
        ]);
    }
}
