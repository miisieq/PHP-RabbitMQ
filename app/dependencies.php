<?php

declare(strict_types=1);

use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        \PhpAmqpLib\Connection\AbstractConnection::class => fn()
            => new \PhpAmqpLib\Connection\AMQPStreamConnection(
                $_ENV['QUEUE_HOST'],
                $_ENV['QUEUE_PORT'],
                $_ENV['QUEUE_USER'],
                $_ENV['QUEUE_PASSWORD']
        )
    ], [
        \Knp\Snappy\Pdf::class => fn()
            => new \Knp\Snappy\Pdf($_ENV['PATH_WKHTMLTOPDF'])
    ]);
};
