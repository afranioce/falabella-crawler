<?php

declare(strict_types=1);

namespace App\Product\Importer;

use App\Crawler\Falabella\CategoryPageCrawler;
use App\Crawler\Falabella\HomePageCrawler;
use App\Product\Product;
use App\Seller\Seller;
use DateTime;
use Linio\Component\Database\DatabaseManager;

class FalabellaImporterStrategy implements ImporterStrategyInterface
{
    private DatabaseManager $db;

    private HomePageCrawler $homePageCrawler;

    private CategoryPageCrawler $categoryPageCrawler;

    public function __construct(
        DatabaseManager $databaseManager,
        HomePageCrawler $homePageCrawler,
        CategoryPageCrawler $categoryPageCrawler
    ) {
        $this->homePageCrawler = $homePageCrawler;
        $this->categoryPageCrawler = $categoryPageCrawler;
        $this->db = $databaseManager;
    }

    public function handler(Seller $seller): void
    {
        foreach ($this->homePageCrawler->getCategoryPathsFromMainMenu($seller) as $categoryPath) {
            $products = $this->categoryPageCrawler->getProductsFromUrl($seller, $categoryPath);

            if (!empty($products)) {
                $this->save($seller, $products);
            }
        }
    }

    /**
     * @param Product[] $products
     */
    private function save(Seller $seller, array $products): void
    {
        foreach ($products as $product) {
            $params = [
                'name' => $product->getTitle(),
                'image' => $product->getPhotos()[0] ?? null,
            ];

            $this->db->execute('
                INSERT INTO `product` (`name`, `image`)
                VALUES
                    (:name, :image)
            ', $params);

            $lastInsertId = $this->db->getLastInsertId();

            foreach ($product->getOffers() as $offer) {
                $params = [
                    'product_id' => $lastInsertId,
                    'seller_id' => $seller->getId(),
                    'name' => $offer->getName(),
                    'link' => $offer->getLink(),
                    'sku' => $offer->getSku(),
                    'price' => $offer->getPrice(),
                    'from_price' => '',
                    'is_manually_reviewed' => (int) $offer->isManualReviewed(),
                    'created_at' => (new DateTime())->format('Y-m-d H:i:s'),
                ];

                $this->db->execute('
                INSERT INTO `offer` (`product_id`, `seller_id`, `name`, `link`, `price`, `from_price`, `sku`, `is_manually_reviewed`, `created_at`)
                VALUES
                    (:product_id, :seller_id, :name, :link, :price, :from_price, :sku, :is_manually_reviewed, :created_at)
            ', $params);
            }
        }
    }
}
