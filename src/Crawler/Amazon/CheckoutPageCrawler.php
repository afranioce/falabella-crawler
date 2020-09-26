<?php

declare(strict_types=1);

namespace App\Crawler\Amazon;

use Crawler\DocumentBuilder;
use Crawler\Property;
use Crawler\Type;

class CheckoutPageCrawler
{
    private DocumentBuilder $documentBuilder;

    public function __construct(DocumentBuilder $documentBuilder)
    {
        $this->documentBuilder = $documentBuilder;
    }

    public function getOfferFreightFromHtml(string $html): void
    {
        $checkoutDocument = $this->documentBuilder->createFromHTML($html);

        $property = new Property([
            'name' => 'order',
            'type' => Type\AssocArrayType::getTypeName(),
            'xpath' => '//div[contains(@class,"order-display")]/div[contains(@class,"one-shipment")]',
            'context' => [
                'properties' => [
                    [
                        'name' => 'items',
                        'type' => Type\AssocArrayType::getTypeName(),
                        'xpath' => '//div[contains(@class,"one-shipment-info")]/div[contains(@class,"order-header")]/div',
                        'context' => [
                            'properties' => [
                                [
                                    'name' => 'address',
                                    'type' => Type\StringType::getTypeName(),
                                    'xpath' => '//div[2]',
                                    'context' => [],
                                ],
                                [
                                    'name' => 'products',
                                    'type' => Type\AssocArrayType::getTypeName(),
                                    'xpath' => '//ul/li',
                                    'context' => [
                                        'properties' => [
                                            [
                                                'name' => 'name',
                                                'type' => Type\StringType::getTypeName(),
                                                'xpath' => '//div/strong',
                                                'context' => [],
                                            ],
                                            [
                                                'name' => 'price',
                                                'type' => Type\StringType::getTypeName(),
                                                'xpath' => '//div/div[1]/span[1]',
                                                'context' => [],
                                            ],
                                            [
                                                'name' => 'quantity',
                                                'type' => Type\StringType::getTypeName(),
                                                'xpath' => '//div/div[1]/span[2]',
                                                'context' => [],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => 'freights',
                        'type' => Type\AssocArrayType::getTypeName(),
                        'xpath' => '//div[contains(@class,"one-shipment-options")]/div/div',
                        'context' => [
                            'properties' => [
                                [
                                    'name' => 'delivery_days',
                                    'type' => Type\StringType::getTypeName(),
                                    'xpath' => '//div[contains(@class, "description")]',
                                    'context' => [],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ], $checkoutDocument);

        $products = $this->transformer($property->getValue());

        $this->save($products);
    }

    private function transformer(array $orders): array
    {
        $products = [];

        foreach ($orders as $order) {
            foreach ($order['items'] as $key => $item) {
                foreach ($item['products'] as $product) {
                    $products[] = [
                        'name' => $product['name'],
                        'offer' => [
                            'price' => $product['price'],
                            'region' => $item['address'],
                            'freights' => $order['freights'][$key]['delivery_days'],
                        ],
                    ];
                }
            }
        }

        return $products;
    }

    private function save(array $products): void
    {
    }
}
