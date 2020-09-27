<?php

declare(strict_types=1);

namespace App\Product\Importer;

use App\Seller\Seller;
use App\Seller\SellerNames;

class ImporterFactory
{
    private FalabellaImporterStrategy $importer;

    public function __construct(FalabellaImporterStrategy $importer)
    {
        $this->importer = $importer;
    }

    public function make(Seller $seller): ImporterStrategyInterface
    {
        if (!\in_array($seller->getName(), SellerNames::ALL, true)) {
            throw new \InvalidArgumentException(sprintf('Seller "%s" is invalid.', $seller->getName()));
        }

        return $this->importer;
    }
}
