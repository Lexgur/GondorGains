<?php

$finder = PhpCsFixer\Finder::create()
    ->in(['src', 'tests'])
    ->exclude('vendor')
    ->name('*.php');

$config = new PhpCsFixer\Config();
$config->setRules([
    '@PSR12' => true,
    '@PhpCsFixer' => true,
    'array_syntax' => ['syntax' => 'short'],
    'no_trailing_whitespace' => true,
])
    ->setFinder($finder);

return $config;
