<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 11/12/2020
 */

declare(strict_types=1);

namespace Tests\JeckelLab\Clock\Clock;

use DateTimeImmutable;
use DateTimeZone;
use JeckelLab\Clock\Clock\FakedClock;
use PHPUnit\Framework\TestCase;

/**
 * Class FakeRunningClockTest
 * @package Tests\JeckelLab\Clock
 */
class FakedClockTest extends TestCase
{
    public function testWithPassedFakeTime(): void
    {
        $time = new DateTimeImmutable('2018-01-01 12:00:00');
        $clock = new FakedClock($time);
        sleep(1);
        $newTime = $clock->now();
        $this->assertGreaterThan($time, $newTime);
        $this->assertEquals(1, $newTime->diff($time)->s);
        $this->assertEquals('2018-01-01 12:00:01', $newTime->format('Y-m-d H:i:s'));
    }

    public function testWithFutureFakeTime(): void
    {
        $time = (new DateTimeImmutable('2040-01-01 12:00:00'));
        $clock = new FakedClock($time);
        sleep(1);
        $newTime = $clock->now();
        $this->assertGreaterThan($time, $newTime);
        $this->assertEquals('2040-01-01 12:00:01', $newTime->format('Y-m-d H:i:s'));
    }

    public function testTimeZone(): void
    {
        $clock = new FakedClock(
            new DateTimeImmutable('2018-01-01 12:00:00'),
            new DateTimeZone('GMT+2')
        );

        $timeWithDflTimezone = $clock->now();
        $this->assertEquals('2018-01-01 14:00:00', $timeWithDflTimezone->format('Y-m-d H:i:s'));
        $this->assertEquals('+02:00', $timeWithDflTimezone->getTimezone()->getName());

        $timeWithUtcTimezone = $clock->now(new DateTimeZone('UTC'));
        $this->assertEquals('2018-01-01 12:00:00', $timeWithUtcTimezone->format('Y-m-d H:i:s'));
        $this->assertEquals('UTC', $timeWithUtcTimezone->getTimezone()->getName());

        // Different timezone, but same universal time
        $this->assertEquals(
            $timeWithDflTimezone->format('U'),
            $timeWithUtcTimezone->format('U')
        );
    }
}
