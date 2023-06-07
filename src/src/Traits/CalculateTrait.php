<?php

namespace App\Traits;


use App\Entity\Sale;
use App\Entity\Tax;

trait CalculateTrait
{
    public function calculatePrice($price, ?Sale $sale = null, ?Tax $tax = null)
    {
        $sale && $price = $this->calculateSale($price, $sale);
        $tax && $price = $this->calculateTax($price, $tax);
        return $price;
    }
    public function calculateSale($price, Sale $sale): float|int
    {
        if ($sale->getSalePrice()) {
            return ($price - $sale->getSalePrice());
        } else {
            return ($price - ($sale->getSalePercent() * 0.01 * $price));
        }
    }
    public function calculateTax($price, Tax $tax): float|int
    {
        return ($tax->getPercent() ? ($price + $price * $tax->getPercent() * 0.01) : $price);
    }
}