<?php

namespace App\Enum;


class ControllerEnum extends BaseEnum
{
    const NAME_PREFIX = 'app';
    const ORDER_NAME = 'order';
    const CALCULATE_NAME = 'calculate';
    const PAYMENT_NAME = 'payment';
    const ORDER_CONTROLLER = self::NAME_PREFIX . '_' . self::ORDER_NAME;
    const CALCULATE_CONTROLLER = self::NAME_PREFIX . '_' . self::CALCULATE_NAME;
    const PAYMENT_CONTROLLER = self::NAME_PREFIX . '_' . self::PAYMENT_NAME;
}