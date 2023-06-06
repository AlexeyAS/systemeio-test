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
        return $this->em->getRepository(Sale::class)->findOneBy(['code'=>$value]);
    }

    public function findByIdProduct($value): ?Product
    {
        return $this->em->getRepository(Product::class)->findOneBy(['id'=>$value]);
    }

    public function getTax($value): int|float
    {
        $product = $this->em->getRepository(Tax::class);
        $tax = $product->findOneBy(['country_code'=>$value]);
        return $tax ? ($tax->getPercent() * 0.01) : 0;
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