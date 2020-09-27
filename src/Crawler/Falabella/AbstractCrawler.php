<?php

declare(strict_types=1);

namespace App\Crawler\Falabella;

use Crawler\Document;
use Crawler\DocumentBuilder;

abstract class AbstractCrawler
{
    protected DocumentBuilder $documentBuilder;

    public function __construct(DocumentBuilder $documentBuilder)
    {
        $this->documentBuilder = $documentBuilder;
    }

    protected function getArrayData(array $data, \Closure $callback): array
    {
        $result = [];

        foreach ($data as $datum) {
            $result[] = $callback($datum);
        }

        return $result;
    }

    protected function getDocument(string $baseUrl, string $path = ''): Document
    {
        return $this->documentBuilder->createFromURL(sprintf('%s%s', $baseUrl, $path));
    }
}
