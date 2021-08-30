<?php

declare(strict_types=1);

namespace Tests\JeckelLab\Clock\Clock;

use DateTimeImmutable;
use Exception;
use JeckelLab\Clock\Clock\RealClock;
use PHPUnit\Framework\TestCase;
use Tests\JeckelLab\Clock\ClockTestCase;

/**
 * Class ClockTest
 * @package Tests\JeckelLab\Clock
 */
final class RealClockTest extends ClockTestCase
{
    public function testConstructorWithoutTimeZone(): void
    {
        $clock = new RealClock();
        $this->assertIsDefaultTimeZone($clock->getTimeZone());
    }

    public function testConstructorWithTimeZone(): void
    {
        $timeZone = new \DateTimeZone('Europe/Paris');
        $clock = new RealClock($timeZone);
        $this->assertEqualsTimeZone($timeZone, $clock->getTimeZone());
    }

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
        $this->assertHasDefaultTimeZone($time);

        // 2nd call return a new DateTimeImmutable
        $this->assertNotSame($time, $clock->now());
    }

    public function testNowOnClockWithTimezone(): void
    {
        $timeZone = new \DateTimeZone("Europe/Paris");
        $clock = new RealClock($timeZone);
        $time = $clock->now();
        $this->assertDateTimeHasTimeZone($timeZone, $time);
    }

    public function testNowWithSpecifiedTimeZone(): void
    {
        $timeZone = new \DateTimeZone("Europe/Paris");
        $clock = new RealClock($timeZone);
        $expectedTimeZone = new \DateTimeZone("America/New_York");
        $time = $clock->now($expectedTimeZone);
        $this->assertDateTimeHasTimeZone($expectedTimeZone, $time);
    }
}
