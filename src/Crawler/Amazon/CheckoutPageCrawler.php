<?php

declare(strict_types=1);

namespace App\Crawler\Amazon;

use App\Seller\Seller;
use Crawler\DocumentBuilder;
use Crawler\Property;
use Crawler\Type;
use DateTime;
use Linio\Component\Database\DatabaseManager;

class CheckoutPageCrawler
{
    private DatabaseManager $db;

    private DocumentBuilder $documentBuilder;

    public function __construct(DatabaseManager $databaseManager, DocumentBuilder $documentBuilder)
    {
        $this->db = $databaseManager;
        $this->documentBuilder = $documentBuilder;
    }

    public function getOfferFreightFromHtml(Seller $seller, string $html): void
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

        $this->save($seller, $products);
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
                            'delivery_days' => $order['freights'][$key]['delivery_days'],
                        ],
                    ];
                }
            }
        }

        return $products;
    }

    private function save($seller, array $products): void
    {
        foreach ($products as $product) {
            $params = [
                'name' => $product['name'],
                'image' => '',
            ];

            $this->db->execute('
                INSERT INTO `product` (`name`, `image`)
                VALUES
                    (:name, :image)
            ', $params);

            $productId = $this->db->getLastInsertId();

            $params = [
                'product_id' => $productId,
                'seller_id' => $seller->getId(),
                'name' => $product['name'],
                'link' => '',
                'sku' => '',
                'price' => $product['offer']['price'],
                'from_price' => '',
                'is_manually_reviewed' => 0,
                'created_at' => (new DateTime())->format('Y-m-d H:i:s'),
            ];

            $this->db->execute('
                INSERT INTO `offer` (`product_id`, `seller_id`, `name`, `link`, `price`, `from_price`, `sku`, `is_manually_reviewed`, `created_at`)
                VALUES
                    (:product_id, :seller_id, :name, :link, :price, :from_price, :sku, :is_manually_reviewed, :created_at)
            ', $params);

            $offerId = $this->db->getLastInsertId();

            $this->db->execute('
                INSERT INTO `region` (`offer_id`, `zipcode`, `delivery_days`, `price`)
                VALUES
                    (:offer_id, :zipcode, :delivery_days, :price)
            ', [
                'offer_id' => $offerId,
                'zipcode' => $product['offer']['region'],
                'delivery_days' => $product['offer']['delivery_days'],
                'price' => $product['offer']['price'],
            ]);
        }
    }
}
