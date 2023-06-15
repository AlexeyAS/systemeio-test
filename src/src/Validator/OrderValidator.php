<?php

namespace App\Validator;

use App\Factory\TaxValidatorFactory;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use App\Validator\Order as OrderConstraint;
use App\Entity\Order;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class OrderValidator extends ConstraintValidator
{
    private TaxValidatorFactory $taxValidatorFactory;

    public function __construct(){
        $this->taxValidatorFactory = new TaxValidatorFactory();
    }
    public function validate($value, Constraint $constraint)
    {
        /* @var OrderConstraint $constraint */
        if (!$constraint instanceof OrderConstraint) {
            throw new UnexpectedTypeException($constraint, OrderConstraint::class);
        }
        if (null === $value || '' === $value) {
            return;
        }
        /** @var Order $value */
        if (!$value instanceof Order) {
            throw new UnexpectedValueException($value, 'App\Entity\Order');
        }

        $countryCode = $value->getCountryCode();
        $taxNumber = $value->getTaxNumber();
        $taxNumberRule = $this->taxValidatorFactory->create($countryCode);


        if (!$value->getCountryCode() || !$value->getTaxNumber()){
            $this->context->buildViolation($constraint->message)->addViolation();
        }
        elseif (preg_match($taxNumberRule['pattern'], $taxNumber) === 0) {
            $this->context->buildViolation($taxNumberRule['message'])->addViolation();
        }
    }
}
