<?php

declare(strict_types=1);

return [
    'dsn' => 'sqlite:' . __DIR__ . '/tmp/dev/GondorGains.sqlite',
    'directory' => __DIR__ . '/src/Migration',
    'migratedRegistryPath' => __DIR__ . '/tmp/dev/migrations.json',
    'controllerDir' => __DIR__ . '/src/Controller',
    'seededRegistryPath' => __DIR__ . '/tmp/dev/seeders.json',
    'seedersDirectory' => __DIR__ . '/src/Seeder',
];
