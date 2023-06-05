<?php

namespace App\Service;

use App\Entity\Product;
use App\Entity\Sale;
use App\Entity\Tax;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;

class Transformer
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }


    public function checkSale($value): ?Sale
    {
        dump($value);
        $sale = $this->em->getRepository(Sale::class);
        dump($sale);
        $find = $sale->findOneBy(['code'=>$value]);


        dump($find);
        return $find;
    }


    public function getTax($value): int|float
    {
        dump($value);
        $product = $this->em->getRepository(Tax::class);
        dump($product);
        $tax = $product->findOneBy(['country_code'=>$value]);


        dump($tax);

        return $tax ? ($tax->getPercent() * 0.01) : 0;
    }

    public function findByIdProduct($value): ?Product
    {
        dump($value);
        $product = $this->em->getRepository(Product::class);
        dump($product);
        $find = $product->findOneBy(['id'=>$value]);


        dump($find);
        return $find;
    }

    /**
     * @return Product[] Returns an array of Product objects
     */
    public function getAllProducts(): array
    {
        $qb = $this->em->createQueryBuilder();
        $query = $qb
            ->select('p.id, p.name, p.price')
            ->from(Product::class, 'p')
            ->getQuery();
        return $query->getResult();
    }
}