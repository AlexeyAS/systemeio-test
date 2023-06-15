<?php

namespace App\Form;

use App\Entity\Order;
use App\Enum\ControllerEnum;
use App\Enum\CountryEnum;
use App\Enum\PaymentProcessorEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Validator\Order as OrderConstraint;

class OrderFormType extends AbstractType
{
    private ValidatorInterface $validator;
    public function __construct(ValidatorInterface $validator) {
        $this->validator = $validator;
    }
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
                        $arr[$product['name']] = $product['id'];
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
                'required' => true,
                'attr' => [
                    'autocomplete' => 'off',
                    'class' => 'form-control form-control-sm'
                ],
                'label_attr' => [ 'class' => 'form-label form-label-sm']
            ])
            ->add('country_code', ChoiceType::class, [
                'label' => 'Страна',
                'choices' => [
                    'Германия' => CountryEnum::COUNTRY_CODE_GERMANY,
                    'Греция' => CountryEnum::COUNTRY_CODE_GREECE,
                    'Франция' => CountryEnum::COUNTRY_CODE_FRANCE,
                    'Италия' => CountryEnum::COUNTRY_CODE_ITALY
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
                    'PayPal' => PaymentProcessorEnum::PAYMENT_PROCESSOR_PAYPAL,
                    'Stripe' => PaymentProcessorEnum::PAYMENT_PROCESSOR_STRIPE,
                ],
                'attr' => [
                    'class' => 'form-control form-control-sm wight-input-short'
                ],
                'label_attr' => [ 'class' => 'form-label form-label-sm']
            ])
            ->add('price', NumberType::class, [
                'label' => 'Price, €;',
                'attr' => [
                    'class' => 'form-control form-control-sm wight-input-short'
                ]
            ])
            ->add(ControllerEnum::CALCULATE_NAME, SubmitType::class, [
                'label' => 'Calculate',
                'attr' => [
                    'class' => 'btn btn-secondary btn-sm mt-4'
                ]
            ])
            ->add(ControllerEnum::PAYMENT_NAME, SubmitType::class, [
                'label' => 'Payment',
                'attr' => [
                    'class' => 'btn btn-primary btn-sm mt-4 '
                ]
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options){});

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) use ($options){

        });

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($options) {
            $form = $event->getForm();
            /** @var Order $data */
            $data = $event->getData();
            $orderConstraint = new OrderConstraint();
            $errors = $this->validator->validate($data, $orderConstraint);

            if (count($errors) > 0){
                /** @var ConstraintViolation $error */
                foreach ($errors as $error){
                    $form->addError(new FormError($error->getMessage()));
                    $event->getForm()->get('tax_number')->addError(new FormError($error->getMessage()));
                }
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            //allow_extra_fields' => true,
            'csrf_protection' => false,
            'data_class' => Order::class,
            'products' => Collection::class,
        ]);
    }
}
