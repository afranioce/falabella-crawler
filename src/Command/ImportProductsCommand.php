<?php

declare(strict_types=1);

namespace App\Command;

use App\Product\Importer\ImporterFactory;
use App\Seller\Seller;
use App\Seller\SellerNames;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportProductsCommand extends Command
{
    protected static $defaultName = 'import:products';

    private ImporterFactory $importerFactory;

    public function __construct(ImporterFactory $importerFactory)
    {
        $this->importerFactory = $importerFactory;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $seller = new Seller(
            SellerNames::FALABELLA_CHILE,
            'https://www.falabella.com/falabella-cl'
        );

        $importer = $this->importerFactory->make($seller);

        $importer->handler($seller);
    }
}
