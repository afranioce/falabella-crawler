<?php

declare(strict_types=1);

namespace App\Crawler\Falabella;

use App\Crawler\CustomType\JsonType;
use App\Seller\Seller;
use Crawler\Property;

class HomePageCrawler extends AbstractCrawler
{
    private const CONTAINER_KEY_CATEGORY_MENU = 'header-med-categories-menu';

    private array $links = [];

    public function getCategoryPathsFromMainMenu(Seller $seller): array
    {
        $homeDocument = $this->getDocument($seller->getHomePage(), '');

        $property = new Property([
            'name' => 'menu',
            'type' => JsonType::getTypeName(),
            'xpath' => '//script[@id="__NEXT_DATA__"]',
            'context' => [],
        ], $homeDocument);

        $arrayData = $property->getValue();

        return $this->getCategoriesData($arrayData['props']['pageProps']['page']['containers']);
    }

    private function getCategoriesData(array $containers): array
    {
        foreach ($containers as $container) {
            if (self::CONTAINER_KEY_CATEGORY_MENU === $container['key']) {
                $this->getRootCategories($container['components'][0]['data']['rootCategories']);

                return $this->links;
            }
        }
    }

    private function getRootCategories(array $rootCategories): array
    {
        return $this->getArrayData(
            $rootCategories,
            fn ($rootCategory): array => $this->getSubCategories($rootCategory['subCategories'])
        );
    }

    private function getSubCategories(array $subCategories): array
    {
        return $this->getArrayData(
            $subCategories,
            fn ($subCategory): array => $this->getLeafCategories($subCategory['leafCategories'])
        );
    }

    private function getLeafCategories(array $leafCategories): array
    {
        return $this->getArrayData($leafCategories, fn ($datum): string => $this->links[] = $datum['link']);
    }
}
