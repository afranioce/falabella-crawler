<?php

declare(strict_types=1);

namespace App\Product;

class Product
{
    private string $title;

    private string $description;

    private string $brand;

    private array $photos;

    /** @var Offer[] */
    private array $offers;

    public function __construct(
        string $title,
        string $description,
        string $brand,
        array $photos,
        array $offers
    ) {
        $this->title = $title;
        $this->description = $description;
        $this->brand = $brand;
        $this->photos = $photos;
        $this->offers = $offers;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getBrand(): string
    {
        return $this->brand;
    }

    public function getPhotos(): array
    {
        return $this->photos;
    }

    /**
     * @return Offer[]
     */
    public function getOffers(): array
    {
        return $this->offers;
    }
}
