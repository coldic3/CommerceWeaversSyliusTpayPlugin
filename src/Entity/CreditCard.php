<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusTpayPlugin\Entity;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;

class CreditCard implements CreditCardInterface
{
    private ?int $id = null;

    private ?string $token = null;
    private ?string $brand = null;
    private ?string $tail = null;

    private ?\DateTimeInterface $expirationDate = null;

    private ?CustomerInterface $customer = null;

    private ?ChannelInterface $channel = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): void
    {
        $this->token = $token;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): void
    {
        $this->brand = $brand;
    }

    public function getTail(): ?string
    {
        return $this->tail;
    }

    public function setTail(?string $tail): void
    {
        $this->tail = $tail;
    }
    public function getExpirationDate(): ?\DateTimeInterface
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(?\DateTimeInterface $expirationDate): void
    {
        $this->expirationDate = $expirationDate;
    }

    public function getCustomer(): ?CustomerInterface
    {
        return $this->customer;
    }

    public function setCustomer(?CustomerInterface $customer): void
    {
        $this->customer = $customer;
    }

    public function getChannel(): ?ChannelInterface
    {
        return $this->channel;
    }

    public function setChannel(?ChannelInterface $channel): void
    {
        $this->channel = $channel;
    }
}
