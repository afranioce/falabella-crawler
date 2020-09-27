<?php

declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;

require __DIR__.'/../vendor/autoload.php';

if (!array_key_exists('APP_ENV', $_SERVER)) {
    $_SERVER['APP_ENV'] = $_ENV['APP_ENV'] ?? null;
}

if ('prod' !== $_SERVER['APP_ENV']) {
    $envFile = __DIR__.'/../.env';
    $testEnvFile = $envFile.'.test';

    if (is_readable($envFile)) {
        if ('test' === $_SERVER['APP_ENV']) {
            (new Dotenv())->usePutenv()->overload($envFile, $testEnvFile);
        } else {
            (new Dotenv())->usePutenv()->load($envFile);
        }
    }
}

$_SERVER['APP_ENV'] = $_ENV['APP_ENV'] = $_SERVER['APP_ENV'] ?: $_ENV['APP_ENV'] ?? 'dev';
$_SERVER['APP_DEBUG'] = $_SERVER['APP_DEBUG'] ?? $_ENV['APP_DEBUG'] ?? 'prod' !== $_SERVER['APP_ENV'];
$_SERVER['APP_DEBUG'] = $_ENV['APP_DEBUG'] = (int) $_SERVER['APP_DEBUG'] || filter_var($_SERVER['APP_DEBUG'], FILTER_VALIDATE_BOOLEAN) ? '1' : '0';
