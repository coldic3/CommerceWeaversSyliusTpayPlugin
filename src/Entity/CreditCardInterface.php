<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Entity;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Resource\Model\ResourceInterface;

interface CreditCardInterface extends ResourceInterface
{
    public function getToken(): ?string;

    public function setToken(?string $token): void;

    public function getBrand(): ?string;

    public function setBrand(?string $brand): void;

    public function getTail(): ?string;

    public function setTail(?string $tail): void;

    public function getExpirationDate(): ?\DateTimeInterface;

    public function setExpirationDate(?\DateTimeInterface $expirationDate): void;

    public function getCustomer(): ?CustomerInterface;

    public function setCustomer(?CustomerInterface $customer): void;
}
