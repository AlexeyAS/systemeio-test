<?php

namespace App\Enum;

class ErrorEnum extends BaseEnum
{
    const ERROR_MAX_PRICE = 'Too high price';
    const ERROR_MIN_PRICE = 'Too low price';
    const ERROR_LOST_RESPONSE = 'Response not exist';
    const ERROR_LOST_ORDER = 'Order not exist';
}