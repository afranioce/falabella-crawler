<?php

declare(strict_types=1);

namespace App\Crawler\Amazon;

use Crawler\DocumentBuilder;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @internal
 * @coversNothing
 */
final class CheckoutPageCrawlerTest extends TestCase
{
    private ObjectProphecy $documentBuilder;

    private CheckoutPageCrawler $checkoutPageCrawler;

    protected function setUp(): void
    {
        $this->documentBuilder = $this->prophesize(DocumentBuilder::class);

        $this->checkoutPageCrawler = new CheckoutPageCrawler(
            $this->documentBuilder->reveal()
        );
    }

    public function testGetGetOfferFreightFromHtml(): void
    {
        $html = file_get_contents(__DIR__.'/../../data/amazon-checkout-once-package-paid-delivery.html');

        $document = (new DocumentBuilder())->createFromHTML($html);

        $this->documentBuilder
            ->createFromHTML(Argument::type('string'))
            ->willReturn($document)
            ->shouldBeCalled()
        ;

        $this->checkoutPageCrawler->getOfferFreightFromHtml($html);
    }
}
