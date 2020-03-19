<?php

declare(strict_types=1);

require __DIR__. '/../vendor/autoload.php';

use DI\ContainerBuilder;
use Symfony\Component\Console\Application;
use Symfony\Component\Dotenv\Dotenv;

(new Dotenv())
    ->load(__DIR__ . '/../.env');

$containerBuilder = new ContainerBuilder();

$dependencies = require __DIR__ . '/dependencies.php';
$dependencies($containerBuilder);

$container = $containerBuilder->build();

$app = new Application();
$commands = require __DIR__ . '/commands.php';
$commands($app, $container);

return $app;
