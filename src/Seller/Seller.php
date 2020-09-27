<?php

declare(strict_types=1);

namespace App\Seller;

class Seller
{
    private int $id;

    private string $name;

    private string $homepage;

    public function __construct(int $id, string $name, string $homepage)
    {
        $this->id = $id;
        $this->name = $name;
        $this->homepage = $homepage;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getHomePage(): string
    {
        return $this->homepage;
    }
}
