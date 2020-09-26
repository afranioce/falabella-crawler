<?php

declare(strict_types=1);

namespace App\Command;

use App\Product\Importer\ImporterFactory;
use App\Seller\Seller;
use App\Seller\SellerNames;
use Symfony\Component\Console\Command\Command;

class ImportProductsCommand extends Command
{
    private ImporterFactory $importerFactory;

    public function __construct(ImporterFactory $importerFactory)
    {
        $this->importerFactory = $importerFactory;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $seller = new Seller(
            SellerNames::FALABELLA_CHILE,
            'https://www.falabella.com/falabella-cl'
        );

        $importer = $this->importerFactory->make($seller);

        $importer->handler();
    }
}
