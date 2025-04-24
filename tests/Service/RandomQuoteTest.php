<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Tests\Service;

use Lexgur\GondorGains\Service\RandomQuote;
use PHPUnit\Framework\TestCase;

class RandomQuoteTest extends TestCase
{
    public function testGetQuote(): void
    {
        $quote = new RandomQuote();
        $quote1 = $quote->getQuote();
        $quote2 = $quote->getQuote();
        $quote3 = $quote->getQuote();
        $quote4 = $quote->getQuote();

        $this->assertNotEquals($quote1, $quote2);
        $this->assertNotEquals($quote2, $quote3);
        $this->assertNotEquals($quote3, $quote4);
    }
}
