<?php

namespace App\Validator;

use App\Enum\CountryEnum;
use App\Enum\TaxEnum;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use App\Validator\Order as OrderConstraint;
use App\Entity\Order;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Validation;

class OrderValidator extends ConstraintValidator
{
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
        $taxNumberRule = [
            'pattern' => TaxEnum::TAX_NUMBER_PATTERN[$countryCode],
            'message' => 'Tax incorrect for template ' . TaxEnum::TAX_NUMBER_MESSAGE[$countryCode]
        ];


        if (!$value->getCountryCode() || !$value->getTaxNumber()){
            $this->context->buildViolation($constraint->message)->addViolation();
        }
        elseif (preg_match($taxNumberRule['pattern'], $taxNumber) === 0) {
            $this->context->buildViolation($taxNumberRule['message'])->addViolation();
        }
    }
}
