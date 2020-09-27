<?php

declare(strict_types=1);

namespace App\Crawler\Falabella;

use Crawler\Document;
use Crawler\DocumentBuilder;
use GuzzleHttp\ClientInterface;

abstract class AbstractCrawler
{
    protected DocumentBuilder $documentBuilder;
    private ClientInterface $client;

    public function __construct(ClientInterface $client, DocumentBuilder $documentBuilder)
    {
        $this->client = $client;
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
//        $response = $this->client->request('GET', sprintf('%s%s', $baseUrl, $path));
//
//        $html = $response->getBody()->getContents();
//        return $this->documentBuilder->createFromHTML($html);

        return $this->documentBuilder->createFromURL(sprintf('%s%s', $baseUrl, $path));
    }
}
