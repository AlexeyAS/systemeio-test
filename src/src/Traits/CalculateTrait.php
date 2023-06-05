<?php

namespace App\Traits;


trait CalculateTrait
{
    public function calculateSale($price, $sale): float|int
    {
        if ($sale['sale_price']) {
            return ($price - $sale['sale_price']);
        } else {
            return ($price - ($sale['sale_percent'] * $price));
        }
    }
    public function calculateTax($price, $tax): float|int
    {
        return $price - $price*$tax;
    }
}