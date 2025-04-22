<?php

declare(strict_types=1);

$controllerDir = __DIR__ . '/tests/ApplicationTest';
if (!empty($_ENV['IS_WEB_TEST'])) {
    $controllerDir = __DIR__ . '/src/Controller';
}

return [
    'dsn' => 'sqlite:' . __DIR__ . '/tmp/test/GondorGainsTest.sqlite',
    'directory' => __DIR__ . '/src/Migration',
    'migratedRegistryPath' => __DIR__ . '/tmp/test/testmigrations.json',
    'controllerDir' => $controllerDir,
];