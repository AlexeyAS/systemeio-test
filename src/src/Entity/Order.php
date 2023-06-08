<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'orders')]
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id',nullable: true, onDelete: null)]
    private null|int|Product $product;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: 'TAX NUMBER CANT BE EMPTY')]
    #[Assert\Valid]
    private ?string $tax_number = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $payment_processor = null;

    #[ORM\Column(length: 2, nullable: true)]
    private ?string $country_code = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sale_code = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $price = null;

    #[ORM\Column(nullable: true)]
    private ?int $timestamp = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): null|int|Product
    {
        return $this->product;
    }

    public function setProduct(null|int|Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getTaxNumber(): ?string
    {
        return $this->tax_number;
    }

    public function setTaxNumber(?string $tax_number): self
    {
        $this->tax_number = $tax_number;

        return $this;
    }

    public function getPaymentProcessor(): ?string
    {
        return $this->payment_processor;
    }

    public function setPaymentProcessor(?string $payment_processor): self
    {
        $this->payment_processor = $payment_processor;

        return $this;
    }

    public function getCountryCode(): ?string
    {
        return $this->country_code;
    }

    public function setCountryCode(?string $country_code): self
    {
        $this->country_code = $country_code;

        return $this;
    }

    public function getSaleCode(): ?string
    {
        return $this->sale_code;
    }

    public function setSaleCode(?string $sale_code): self
    {
        $this->sale_code = $sale_code;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getTimestamp(): ?int
    {
        return $this->timestamp;
    }

    public function setTimestamp(?int $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function jsonSerialize(): ?array
    {
        return [
            'id' => $this->getId(),
            'product' => $this->getProduct(),
            'country_code' => $this->getCountryCode(),
            'tax_number' => $this->getTaxNumber(),
            'payment_processor' => $this->getPaymentProcessor(),
            'sale_code' => $this->getSaleCode(),
            'price' => $this->getPrice(),
            'timestamp' => $this->getTimestamp()
        ];
    }
}
