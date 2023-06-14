<?php

namespace App\Enum;

use App\Enum\CalculateEnum;
use App\Enum\OrderEnum;
use App\Enum\PaymentEnum;

class ControllerEnum extends BaseEnum
{
    const NAME_PREFIX = 'app';
    const ORDER_CONTROLLER = self::NAME_PREFIX . '_' . OrderEnum::ORDER_NAME;
    const CALCULATE_CONTROLLER = self::NAME_PREFIX . '_' . CalculateEnum::CALCULATE_NAME;
    const PAYMENT_CONTROLLER = self::NAME_PREFIX . '_' . PaymentEnum::PAYMENT_NAME;
}