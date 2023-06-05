<?php

namespace App\Form;

use App\Entity\Order;
use App\Entity\Product;
use App\Enum\Country;
use App\Enum\PaymentProcessor;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Collection;

class OrderType extends AbstractType
{
//    public function __construct(private readonly ManagerRegistry $doctrine) {}
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('product', ChoiceType::class, [
                'label' => 'Товар',
                'choice_loader' => new CallbackChoiceLoader(function() use($options) {

                    $products = $options['products'];
                    if (!$products){
                        return [];
                    }
                    $arr = [];
                    foreach ($products as $product){
                        $arr[$product['name']] = $product['price'];
                    }
                    return $arr;
                }),
                'attr' => [
                    'class' => 'form-control form-control-sm wight-input-short'
                ],
                'label_attr' => [ 'class' => 'form-label form-label-sm']
            ])
            ->add('tax_number', TextType::class, [
                'label' => 'Налоговый номер',
                'required' => false,
                'attr' => [
                    'autocomplete' => 'off',
                    'class' => 'form-control form-control-sm'
                ],
                'label_attr' => [ 'class' => 'form-label form-label-sm']
            ])
            ->add('country_code', ChoiceType::class, [
                'label' => 'Страна',
                'choices' => [
                    'Германия' => Country::COUNTRY_CODE_GERMANY,
                    'Греция' => Country::COUNTRY_CODE_GREECE,
                    'Франция' => Country::COUNTRY_CODE_FRANCE,
                    'Италия' => Country::COUNTRY_CODE_ITALY
                ],
                'attr' => [
                    'class' => 'form-control form-control-sm wight-input-short'
                ],
                'label_attr' => [ 'class' => 'form-label form-label-sm']
            ])
            ->add('sale_code', TextType::class, [
                'label' => 'Купон со скидкой',
                'required' => false,
                'attr' => [
                    'autocomplete' => 'off',
                    'class' => 'form-control form-control-sm'
                ],
                'label_attr' => [ 'class' => 'form-label form-label-sm']
            ])
            ->add('payment_processor', ChoiceType::class, [
                'label' => 'Способ оплаты',
                'choices' => [
                    'PayPal' => PaymentProcessor::PAYMENT_CODE_PAYPAL,
                    'Stripe' => PaymentProcessor::PAYMENT_CODE_STRIPE,
                ],
                'attr' => [
                    'class' => 'form-control form-control-sm wight-input-short'
                ],
                'label_attr' => [ 'class' => 'form-label form-label-sm']
            ])


            ->add('price')
//            ->add('product_id')
//            ->add('product_name')
            ->add('submit', SubmitType::class, [
                'label' => 'Рассчитать',
                'attr' => [
                    'class' => 'btn btn-sm mt-4 '
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
            'products' => Collection::class,
        ]);
    }
}
