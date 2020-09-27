<?php

declare(strict_types=1);

namespace App\Crawler\Falabella;

use App\Crawler\CustomType\JsonType;
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
final class HomePageCrawlerTest extends TestCase
{
    private ObjectProphecy $documentBuilder;

    private HomePageCrawler $homePageCrawler;

    protected function setUp(): void
    {
        $this->documentBuilder = $this->prophesize(DocumentBuilder::class);

        $this->homePageCrawler = new HomePageCrawler(
            $this->documentBuilder->reveal(),
        );
    }

    public function testGetProductsFromUrl(): void
    {
        $html = file_get_contents(__DIR__.'/../../data/falabella-home.html');

        $config = new Configuration([
            'types' => [
                JsonType::TYPE_NAME => JsonType::class,
            ],
        ]);

        $document = (new DocumentBuilder($config))->createFromHTML($html);

        $this->documentBuilder
            ->createFromURL(Argument::is('http://fake.url'))
            ->willReturn($document)
            ->shouldBeCalled()
        ;

        $seller = new Seller(1, 'foobar', 'http://fake.url');

        $categoryPaths = $this->homePageCrawler->getCategoryPathsFromMainMenu($seller);

        $expected = [
            '/category/cat90084/Autos-a-Bateria',
        ];

        static::assertEquals($expected, $categoryPaths);
    }
}
