<?php

declare(strict_types=1);

namespace App\Crawler\Falabella;

use App\Seller\Seller;
use Crawler\Document;
use Crawler\DocumentBuilder;

abstract class AbstractCrawler
{
    protected DocumentBuilder $documentBuilder;
    private Seller $seller;

    public function __construct(Seller $seller, DocumentBuilder $documentBuilder)
    {
        $this->documentBuilder = $documentBuilder;
        $this->seller = $seller;
    }

    protected function getArrayData(array $data, \Closure $callback): array
    {
        $result = [];

        foreach ($data as $datum) {
            $result[] = $callback($datum);
        }

        return $result;
    }

    protected function getDocument(string $path = ''): Document
    {
        return $this->documentBuilder->createFromURL(sprintf('%s/%s', $this->seller->getHomePage(), $path));
    }
}
