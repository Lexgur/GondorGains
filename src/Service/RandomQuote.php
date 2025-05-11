<?php

declare(strict_types=1);

namespace Lexgur\GondorGains\Service;

class RandomQuote
{
    /** @var array|string[] */
    private array $randomQuotes = [
        'Even the smallest person can change the course of the future. – Galadriel',
        'There is some good in this world, and it’s worth fighting for. – Samwise Gamgee',
        'All we have to decide is what to do with the time that is given us. – Gandalf',
        'You step into the Road, and if you don’t keep your feet, there is no knowing where you might be swept off to. – Frodo Baggins',
        'A day may come when the courage of men fails... but it is not this day. – Aragorn',
        'Deeds will not be less valiant because they are unpraised. – Aragorn',
        'I will not say: do not weep; for not all tears are an evil. – Gandalf',
        'The world is indeed full of peril, and in it there are many dark places; but still there is much that is fair. – Haldir',
        'The strength of the body, like the will of the heart, can carry you through the darkest paths. – Legolas',
        'Every step you take is a defiance of doubt, a march toward victory. – Éowyn',
        'Do not falter now, for the burden you carry shapes the hero you become. – Aragorn',
        'Rise again, as the dawn rises, and let your resolve shine brighter than mithril. – Gandalf',
        'The fire within you burns stronger than any forge; let it fuel your next rep. – Gimli',
        'A single lift, like a single choice, can change the fate of all. – Frodo Baggins',
        'Endurance is not given; it is forged in the sweat of struggle. – Boromir',
        'You are no mere wanderer; each stride makes you a warrior of the gym. – Arwen',
    ];
    private ?string $lastQuote;

    public function __construct()
    {
        $this->lastQuote = $_SESSION['last_quote'] ?? null;
    }

    public function getQuote(): string
    {
        $quoteQueue = array_filter($this->randomQuotes, function($quote) {
            return $quote !== $this->lastQuote;
        });

        $quote = $quoteQueue[array_rand($quoteQueue)];
        $this->lastQuote = $quote;

        $_SESSION['last_quote'] = $quote;
        return $quote;
    }
}
