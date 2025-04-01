<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Controller;

use Lexgur\GondorGains\Attribute\Path;

#[Path('/about')]
class AboutProjectController
{
    public function about(): string
    {
        return 'This is about the project';
    }
}
