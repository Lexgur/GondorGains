<?php

$finder = PhpCsFixer\Finder::create()
    ->in(['src', 'test'])
    ->exclude('vendor')
    ->name('*.php');

$config = new PhpCsFixer\Config();
$config->setRules([
    '@PSR12' => true,
    '@PhpCsFixer' => true,
    'array_syntax' => ['syntax' => 'short'],
    'no_trailing_whitespace' => true,

    'general_phpdoc_annotation_remove' => [
        'annotations' => ['internal', 'coversNothing'],
    ],
])
    ->setFinder($finder);

return $config;
