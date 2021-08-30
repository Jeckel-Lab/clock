<?php

declare(strict_types=1);

namespace Tests\JeckelLab\Clock\Clock;

use DateTimeImmutable;
use DateTimeZone;
use JeckelLab\Clock\Clock\FrozenClock;
use Tests\JeckelLab\Clock\ClockTestCase;

/**
 * Class FakeClockTest
 * @package Test\Jeckel\Clock
 */
final class FrozenClockTest extends ClockTestCase
{
    public function testConstructorWithoutTimeZone(): void
    {
        $initTime = new DateTimeImmutable('2018-01-01 12:00:00');
        $clock = new FrozenClock($initTime);
        $this->assertIsDefaultTimeZone($clock->getTimeZone());
    }

    public function testConstructorWithInitDateTimeZoned(): void
    {
        $initTimeZone = new DateTimeZone('Europe/Paris');
        $initTime = new DateTimeImmutable('2018-01-01 12:00:00', $initTimeZone);
        $clock = new FrozenClock($initTime);
        $this->assertEqualsTimeZone($initTimeZone, $clock->getTimeZone());
    }

    public function testConstructorWithTimeZone(): void
    {
        $initTimeZone = new DateTimeZone('Europe/Paris');
        $initTime = new DateTimeImmutable('2018-01-01 12:00:00', $initTimeZone);
        $clock = new FrozenClock($initTime, $initTimeZone);
        $this->assertEqualsTimeZone($initTimeZone, $clock->getTimeZone());
    }

    public function testNowWithDefaultTimeZone(): void
    {
        $initTime = new DateTimeImmutable('2018-01-01 12:00:00');
        $clock = new FrozenClock($initTime);
        $now = $clock->now();
        $this->assertSameUniversalTime($initTime, $now);
        $this->assertHasDefaultTimeZone($now);
    }

    public function testNowWithDefinedClockTimeZone(): void
    {
        $initTimeZone = new DateTimeZone('Europe/Paris');
        $initTime = new DateTimeImmutable('2018-01-01 12:00:00', $initTimeZone);
        $clock = new FrozenClock($initTime);
        $now = $clock->now();
        $this->assertSameUniversalTime($now, $initTime);
        $this->assertDateTimeHasTimeZone($initTimeZone, $now);
    }

    public function testNowWithRequestedTimeZone(): void
    {
        $initTimeZone = new DateTimeZone('Europe/Paris');
        $initTime = new DateTimeImmutable('2018-01-01 12:00:00', $initTimeZone);
        $clock = new FrozenClock($initTime);

        $nowTimeZone = new DateTimeZone('America/Los_Angeles');
        $time = $clock->now($nowTimeZone);

        $this->assertDateTimeHasTimeZone($nowTimeZone, $time);
        // Universal time is the same, only timezone is different
        $this->assertSameUniversalTime($initTime, $time);
    }

    public function testConstructorWithInitTimeZoneDifferentThanSpecifiedOne(): void
    {
        $initTimeZone = new DateTimeZone('Europe/Paris');
        $initTime = new DateTimeImmutable('2018-01-01 12:00:00', new DateTimeZone('America/New_York'));
        $clock = new FrozenClock($initTime, $initTimeZone);
        $this->assertEqualsTimeZone($initTimeZone, $clock->getTimeZone());

        $now = $clock->now();
        $this->assertSameUniversalTime($initTime, $now);
        $this->assertDateTimeHasTimeZone($initTimeZone, $now);
    }

    public function testSetTo(): void
    {
        $timeZone = new DateTimeZone('Europe/Paris');
        $clock = new FrozenClock(
            new DateTimeImmutable('2016-01-01 12:30:00', $timeZone)
        );

        $updateInitTime = new DateTimeImmutable('2018-01-01 12:00:00');
        $this->assertHasDefaultTimeZone($updateInitTime);

        $clock->setClock($updateInitTime);
        $time = $clock->now();

        $this->assertSameUniversalTime($updateInitTime, $time);
        $this->assertHasNotDefaultTimeZone($time);
        $this->assertDateTimeHasTimeZone($timeZone, $time);
    }
}
