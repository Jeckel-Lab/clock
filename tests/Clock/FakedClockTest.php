<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 11/12/2020
 */

declare(strict_types=1);

namespace Tests\JeckelLab\Clock\Clock;

use Cassandra\Date;
use DateTimeImmutable;
use DateTimeZone;
use JeckelLab\Clock\Clock\FakedClock;
use Tests\JeckelLab\Clock\ClockTestCase;

/**
 * Class FakeRunningClockTest
 * @package Tests\JeckelLab\Clock
 */
class FakedClockTest extends ClockTestCase
{
    public function testConstructorWithoutTimeZone(): void
    {
        $initTime = new DateTimeImmutable('2018-01-01 12:00:00');
        $clock = new FakedClock($initTime);
        $this->assertIsDefaultTimeZone($clock->getTimeZone());
    }

    public function testConstructorWithInitDateTimeZoned(): void
    {
        $initTimeZone = new DateTimeZone('Europe/Paris');
        $initTime = new DateTimeImmutable('2018-01-01 12:00:00', $initTimeZone);
        $clock = new FakedClock($initTime);
        $this->assertEqualsTimeZone($initTimeZone, $clock->getTimeZone());
    }

    public function testConstructorWithTimeZone(): void
    {
        $initTimeZone = new DateTimeZone('Europe/Paris');
        $initTime = new DateTimeImmutable('2018-01-01 12:00:00', $initTimeZone);
        $clock = new FakedClock($initTime, $initTimeZone);
        $this->assertEqualsTimeZone($initTimeZone, $clock->getTimeZone());
    }

    public function testWithPassedFakeTime(): void
    {
        $time = new DateTimeImmutable('2018-01-01 12:00:00');
        $clock = new FakedClock($time);
        sleep(1);
        $now = $clock->now();
        $this->assertGreaterThan($time, $now);
        $this->assertEquals(1, $now->diff($time)->s);
        $this->assertEquals('2018-01-01 12:00:01', $now->format('Y-m-d H:i:s'));
        $this->assertHasDefaultTimeZone($now);
    }

    public function testWithFutureFakeTime(): void
    {
        $time = (new DateTimeImmutable('2040-01-01 12:00:00'));
        $clock = new FakedClock($time);
        sleep(1);
        $now = $clock->now();
        $this->assertGreaterThan($time, $now);
        $this->assertEquals('2040-01-01 12:00:01', $now->format('Y-m-d H:i:s'));
        $this->assertHasDefaultTimeZone($now);
    }

    public function testTimeZone(): void
    {
        $initTimeZone = new DateTimeZone('Europe/Paris');
        $initDateTime = new DateTimeImmutable('2018-01-01 12:00:00', $initTimeZone);
        $clock = new FakedClock($initDateTime);

        sleep(1);
        $now = $clock->now();
        $this->assertSameUniversalTime($initDateTime->add(new \DateInterval('PT1S')), $now);
        $this->assertDateTimeHasTimeZone($initTimeZone, $now);

        $expectedTimeZone = new DateTimeZone('America/New_York');
        $nowWithDifferentTimezone = $clock->now($expectedTimeZone);
        $this->assertSameUniversalTime(
            $initDateTime->add(new \DateInterval('PT1S')),
            $nowWithDifferentTimezone
        );
        $this->assertDateTimeHasTimeZone($expectedTimeZone, $nowWithDifferentTimezone);

        // Different timezone, but same universal time
        $this->assertSameUniversalTime($nowWithDifferentTimezone, $now);
    }
}
