<?php
declare(strict_types=1);

namespace Test\Jeckel\Clock;

use DateTimeImmutable;
use Jeckel\Clock\Clock;
use PHPUnit\Framework\TestCase;

final class ClockTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testNow()
    {
        $now = new DateTimeImmutable('now');
        $service = new Clock();
        $systemClock = $service->now();
        $this->assertGreaterThanOrEqual($now, $systemClock);
        $this->assertLessThanOrEqual(new DateTimeImmutable('now'), $systemClock);
    }
}
