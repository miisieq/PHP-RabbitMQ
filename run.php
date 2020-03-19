#!/usr/bin/env php
<?php

declare(strict_types=1);

/** @var \Symfony\Component\Console\Application $app */
$app = require __DIR__ . '/app/bootstrap.php';
$app->run();
