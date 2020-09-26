<?php

declare(strict_types=1);

namespace App\Product\Importer;

interface ImporterStrategyInterface
{
    public function handler(): void;
}
