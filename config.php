<?php

declare(strict_types=1);

$root = __DIR__;
$environment = $_SERVER['APP_ENV'] ?? 'dev';

$config = [
    'root' => $root,
    'filesystem' => $root . '/tmp/' . $environment,
];

$environmentConfigFile = $root . '/' . $environment . '.config.php';
if (file_exists($environmentConfigFile)) {
    /** @var array<string, mixed> $environmentConfig **/
    $environmentConfig = require $environmentConfigFile;
}

return $environmentConfig + $config;
