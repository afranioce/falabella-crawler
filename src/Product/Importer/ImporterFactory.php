<?php

declare(strict_types=1);

namespace App\Product\Importer;

use App\Seller\Seller;
use App\Seller\SellerNames;

class ImporterFactory
{
    /** @var ImporterStrategyInterface[] */
    private array $importers;

    public function __construct(array $importers)
    {
        $this->importers = $importers;
    }

    public function make(Seller $seller): ImporterStrategyInterface
    {
        if (\in_array($seller->getName(), SellerNames::ALL, true)) {
            throw new \InvalidArgumentException(sprintf('Seller "%s" is invalid.', $seller->getName()));
        }

        return $this->importers[$seller->getName()];
    }
}
