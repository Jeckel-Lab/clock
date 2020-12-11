<?php

declare(strict_types=1);

namespace Tests\JeckelLab\Clock\Clock;

use DateTimeImmutable;
use Exception;
use JeckelLab\Clock\Clock\RealClock;
use PHPUnit\Framework\TestCase;

/**
 * Class ClockTest
 * @package Tests\JeckelLab\Clock
 */
final class RealClockTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testNow(): void
    {
        $now = new DateTimeImmutable('now');
        $clock = new RealClock();
        $time = $clock->now();
        $this->assertGreaterThanOrEqual($now, $time);
        $this->assertLessThanOrEqual(new DateTimeImmutable('now'), $time);

        // 2nd call return a new DateTimeImmutable
        $this->assertNotSame($time, $clock->now());
    }

    public function testNowOnClockWithTimezone(): void
    {
        $timeZone = new \DateTimeZone("Europe/Paris");
        $clock = new RealClock($timeZone);
        $time = $clock->now();
        $this->assertEquals($timeZone->getName(), $time->getTimezone()->getName());
    }

    public function testDefaultTimeZone(): void
    {
        $this->assertEquals(date_default_timezone_get(), (new RealClock)->now()->getTimezone()->getName());
    }
}
