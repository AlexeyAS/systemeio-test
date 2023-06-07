<?php

namespace App\Enum;

class OrderEnum extends BaseEnum
{
    const ORDER_NAME = 'order';
    const ORDER_ACTION_CALCULATE = 'calculate';
    const ORDER_ACTION_PAYMENT = PaymentEnum::NAME;

}