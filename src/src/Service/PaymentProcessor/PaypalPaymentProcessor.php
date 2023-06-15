<?php

namespace App\Service\PaymentProcessor;
use Exception;

class PaypalPaymentProcessor
{
    /**
     * @throws Exception in case of a failed payment
     */
    public function pay(int $price): bool|string
    {
        if ($price > 100) {
            return 'Too high price';
        }

        //process payment logic
        return true;
    }
}