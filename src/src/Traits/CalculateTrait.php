<?php

namespace App\Traits;


use App\Entity\Sale;

trait CalculateTrait
{
    public function calculateSale($price, Sale $sale): float|int
    {
        if ($sale->getSalePrice()) {
            return ($price - $sale->getSalePrice());
        } else {
            return ($price - ($sale->getSalePercent() * 0.01 * $price));
        }
    }
    public function calculateTax($price, $tax): float|int
    {
        return $price + $price*$tax;
    }
}