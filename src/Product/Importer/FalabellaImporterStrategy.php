<?php

declare(strict_types=1);

namespace App\Product\Importer;

use App\Crawler\Falabella\CategoryPageCrawler;
use App\Crawler\Falabella\HomePageCrawler;

class FalabellaImporterStrategy implements ImporterStrategyInterface
{
    private HomePageCrawler $homePageCrawler;
    private CategoryPageCrawler $categoryPageCrawler;

    public function __construct(HomePageCrawler $homePageCrawler, CategoryPageCrawler $categoryPageCrawler)
    {
        $this->homePageCrawler = $homePageCrawler;
        $this->categoryPageCrawler = $categoryPageCrawler;
    }

    public function handler(): void
    {
        foreach ($this->homePageCrawler->getCategoryPathsFromMainMenu() as $categoryPath) {
            $products = $this->categoryPageCrawler->getProductsFromUrl($categoryPath);

            if (!empty($products)) {
                $this->save($products);
            }
        }
    }

    private function save(array $products): void
    {
    }
}
