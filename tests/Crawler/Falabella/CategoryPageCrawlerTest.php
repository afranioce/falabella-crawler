<?php

declare(strict_types=1);

namespace App\Crawler\Falabella;

use App\Crawler\CustomType\JsonType;
use App\Product\Offer;
use App\Product\Product;
use App\Seller\Seller;
use Crawler\Configuration;
use Crawler\DocumentBuilder;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @internal
 * @coversNothing
 */
final class CategoryPageCrawlerTest extends TestCase
{
    private ObjectProphecy $documentBuilder;

    private CategoryPageCrawler $categoryPageCrawler;

    protected function setUp(): void
    {
        $this->documentBuilder = $this->prophesize(DocumentBuilder::class);

        $this->categoryPageCrawler = new CategoryPageCrawler(
            $this->documentBuilder->reveal(),
        );
    }

    public function testGetProductsFromUrl(): void
    {
        $html = file_get_contents(__DIR__.'/../../data/falabella-category.html');

        $config = new Configuration([
            'types' => [
                JsonType::TYPE_NAME => JsonType::class,
            ],
        ]);

        $document = (new DocumentBuilder($config))->createFromHTML($html);

        $this->documentBuilder
            ->createFromURL(Argument::is('http://fake.url/foobar'))
            ->willReturn($document)
            ->shouldBeCalled()
        ;

        $seller = new Seller(1, 'foobar', 'http://fake.url');

        $products = $this->categoryPageCrawler->getProductsFromUrl($seller, '/foobar');

        $expected = [
            new Product(
                'Camión Bomberos',
                "Velocidad máxima: 5KM/H\nProfundidad: 115 cm",
                'Scoop',
                [''],
                [
                    new Offer(
                        'Camión Bomberos (Oferta)',
                        'http://fake.url/falabella-cl/product/881631269/Camion-Bomberos',
                        209.99,
                        '881631269'
                    ),
                    new Offer(
                        'Camión Bomberos',
                        'http://fake.url/falabella-cl/product/881631269/Camion-Bomberos',
                        299.99,
                        '881631269'
                    ),
                ]
            ),
            new Product(
                'Auto a Batería BMW Serie 4 6V Rojo',
                'Nivel: Medio',
                'Bmw',
                [''],
                [
                    new Offer(
                        'Auto a Batería BMW Serie 4 6V Rojo',
                        'http://fake.url/falabella-cl/product/880973400/Auto-a-Bateria-BMW-Serie-4-6V-Rojo',
                        159.99,
                    '880973400'
                    ),
                ]
            ),
        ];

        static::assertEquals($expected, $products);
    }
}
