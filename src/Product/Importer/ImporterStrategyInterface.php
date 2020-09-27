<?php

declare(strict_types=1);

namespace App\Product\Importer;

use App\Seller\Seller;

interface ImporterStrategyInterface
{
    public function handler(Seller $seller): void;
}
