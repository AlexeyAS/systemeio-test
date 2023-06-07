<?php

namespace App\Service;

use App\Entity\Order;
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

    public function calcData(int $productId,
                             ?int $saleId,
                             ?int $taxId,
                             string $price,
                             ?string $taxNumber,
                             ?string $countryCode,
                             ?string $saleCode,
                             ?string $paymentProcessor): string
    {
        $productId && $result['product_id'] = $productId;
        $saleId && $result['sale_id'] = $saleId;
        $taxId && $result['tax_id'] = $taxId;
        $price && $result['price'] = $price;
        $taxNumber && $result['tax_number'] = $taxNumber;
        $countryCode && $result['country_code'] = $countryCode;
        $saleCode && $result['sale_code'] = $saleCode;
        $paymentProcessor && $result['payment_processor'] = $paymentProcessor;
        dump($result??'');
        return (http_build_query($result ?? []));
    }
}