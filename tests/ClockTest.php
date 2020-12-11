<?php

declare(strict_types=1);

namespace Tests\JeckelLab\Clock;

use DateTimeImmutable;
use Exception;
use JeckelLab\Clock\Clock;
use PHPUnit\Framework\TestCase;

/**
 * Class ClockTest
 * @package Tests\JeckelLab\Clock
 */
final class ClockTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testNow(): void
    {
        $now = new DateTimeImmutable('now');
        $clock = new Clock();
        $time = $clock->now();
        $this->assertGreaterThanOrEqual($now, $time);
        $this->assertLessThanOrEqual(new DateTimeImmutable('now'), $time);

        // 2nd call return a new DateTimeImmutable
        $this->assertNotSame($time, $clock->now());
    }

    public function testNowOnClockWithTimezone(): void
    {
        $timeZone = new \DateTimeZone("Europe/Paris");
        $clock = new Clock($timeZone);
        $time = $clock->now();
        $this->assertEquals($timeZone->getName(), $time->getTimezone()->getName());
    }

    public function testDefaultTimeZone(): void
    {
        $this->assertEquals(date_default_timezone_get(), (new Clock)->now()->getTimezone()->getName());
    }
}
