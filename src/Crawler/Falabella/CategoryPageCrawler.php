<?php

declare(strict_types=1);

namespace App\Crawler\Falabella;

use App\Crawler\CustomType\JsonType;
use App\Product\Offer;
use App\Product\Product;
use Crawler\Property;

class CategoryPageCrawler extends AbstractCrawler
{
    public function getProductsFromUrl(string $path)
    {
        $categoryDocument = $this->getDocument($path);

        $property = new Property([
            'name' => 'categories',
            'type' => JsonType::getTypeName(),
            'xpath' => '//script[@id="__NEXT_DATA__"]',
            'context' => [],
        ], $categoryDocument);

        $arrayData = $property->getValue();

        return $this->getProducts($arrayData['props']['pageProps']['results']);
    }

    /**
     * @return Product[]
     */
    private function getProducts(array $products): array
    {
        return $this->getArrayData(
            $products,
            fn ($product): Product => new Product(
                $product['displayName'],
                implode(PHP_EOL, $product['topSpecifications']),
                $product['brand'],
                // TODO pegar as imagens
                [''],
                array_map(
                    fn (array $price): Offer => new Offer(
                        trim(sprintf('%s %s', $product['displayName'], $price['label'])),
                        $product['url'],
                        (float) current($price['price'])
                    ),
                    $product['prices']
                )
            )
        );
    }
}
