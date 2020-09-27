<?php

namespace App\Action;

use App\Crawler\Amazon\CheckoutPageCrawler;
use App\Seller\Seller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AmazonRegionAction
{
    private CheckoutPageCrawler $checkoutPageCrawler;

    public function __construct(CheckoutPageCrawler $checkoutPageCrawler)
    {
        $this->checkoutPageCrawler = $checkoutPageCrawler;
    }

    public function __invoke(Request $request) : Response
    {
        $body = $request->request->all();

        $seller = new Seller(
            2,
            'amazon-us',
            'https://www.amazon.com'
        );

        $this->checkoutPageCrawler->getOfferFreightFromHtml($seller, $body['html']);
    }
}
