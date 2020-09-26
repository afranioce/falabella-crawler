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
    /** @var ObjectProphecy */
    private $seller;

    /** @var ObjectProphecy */
    private $documentBuilder;

    private HomePageCrawler $homePageCrawler;

    protected function setUp(): void
    {
        $this->seller = $this->prophesize(Seller::class);
        $this->documentBuilder = $this->prophesize(DocumentBuilder::class);

        $this->homePageCrawler = new HomePageCrawler(
            $this->seller->reveal(),
            $this->documentBuilder->reveal(),
        );
    }

    public function testGetProductsFromUrl(): void
    {
        $this->seller
            ->getHomepage()
            ->willReturn('http://fake.url')
            ->shouldBeCalled()
        ;

        $html = file_get_contents(__DIR__.'/../../data/falabella-home.html');

        $config = new Configuration([
            'types' => [
                JsonType::TYPE_NAME => JsonType::class,
            ],
        ]);

        $document = (new DocumentBuilder($config))->createFromHTML($html);

        $this->documentBuilder
            ->createFromURL(Argument::is('http://fake.url/'))
            ->willReturn($document)
            ->shouldBeCalled()
        ;

        $categoryPaths = $this->homePageCrawler->getCategoryPathsFromMainMenu();

        $expected = [
            '/category/cat90084/Autos-a-Bateria',
        ];

        static::assertEquals($expected, $categoryPaths);
    }
}
