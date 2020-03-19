<?php

declare(strict_types=1);

use Knp\Snappy\Pdf;
use PhpAmqpLib\Connection\AbstractConnection;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;

return function (Application $application, ContainerInterface $container) {
    $application->add(
        new \App\ProducerCommand(
            $container->get(AbstractConnection::class)
        )
    );
    $application->add(
        new \App\ConsumerCommand(
            $container->get(AbstractConnection::class),
            $container->get(Pdf::class)
        )
    );
};
