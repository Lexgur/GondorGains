<?php

declare(strict_types=1);

namespace Lexgur\GondorGains;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TemplateProvider
{
    private ?Environment $twig = null;

    private string $templatePath;

    private string $cache;

    public function __construct(string $templatePath, string $cache) 
    {
        $this->templatePath = $templatePath;
        $this->cache = $cache;
    }

    public function get(): Environment
    {
        if (null === $this->twig) {
            $loader = new FilesystemLoader($this->templatePath);
            $this->twig = new Environment($loader, [
                'cache' => $this->cache,
            ]);
        }

        return $this->twig;
    }
}