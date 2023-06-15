<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\Product;
use App\Entity\Sale;
use App\Entity\Tax;
use Doctrine\ORM\EntityManagerInterface;

class Finder
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function getSale($value): ?Sale
    {
        return $this->em->getRepository(Sale::class)->findOneBy(['code'=>$value]);
    }

    public function getTax($value): ?Tax
    {
        return $this->em->getRepository(Tax::class)->findOneBy(['country_code'=>$value]);
    }

    public function findByIdProduct($value): ?Product
    {
        return $this->em->getRepository(Product::class)->findOneBy(['id'=>$value]);
    }

    public function findByIdOrder($value): ?Order
    {
        return $this->em->getRepository(Order::class)->findOneBy(['id'=>$value]);
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