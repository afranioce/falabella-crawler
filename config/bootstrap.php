<?php

declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;

set_time_limit(0);

require dirname(__DIR__) . '/vendor/autoload.php';

$envFile = dirname(__DIR__) . '/.env';

if (is_readable($envFile)) {
    (new Dotenv())->load($envFile);
}
