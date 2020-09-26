<?php

declare(strict_types=1);

namespace App\Product;

class Offer
{
    private string $name;

    private string $link;

    private float $price;

    private bool $isManualReviewed = false;

    public function __construct(
        string $name,
        string $link,
        float $price
    ) {
        $this->name = $name;
        $this->link = $link;
        $this->price = $price;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function isManualReviewed(): bool
    {
        return $this->isManualReviewed;
    }
}
