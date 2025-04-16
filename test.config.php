<?php

declare(strict_types=1);

return [
    'dsn' => 'sqlite:' . __DIR__ . '/tmp/test/GondorGainsTest.sqlite',
    'directory' => __DIR__ . '/src/Migration',
    'migratedRegistryPath' => __DIR__ . '/tmp/test/testmigrations.json',
    'templatePath' => __DIR__ . '/tmp/test/templates/',
];
