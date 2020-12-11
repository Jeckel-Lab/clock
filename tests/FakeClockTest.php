<?php

declare(strict_types=1);

namespace Tests\JeckelLab\Clock;

use DateTimeZone;
use JeckelLab\Clock\FakeClock;
use PHPUnit\Framework\TestCase;

/**
 * Class FakeClockTest
 * @package Test\Jeckel\Clock
 */
final class FakeClockTest extends TestCase
{
    public function testNow(): void
    {
        $time = new \DateTimeImmutable('2018-01-01 12:00:00');
        $clock = new FakeClock($time);
        $this->assertSame($time, $clock->now());
    }

    public function testSetTo(): void
    {
        $time = new \DateTimeImmutable('2018-01-01 12:00:00');
        $clock = new FakeClock(new \DateTimeImmutable('2016-01-01 12:30:00'));
        $clock->setClock($time);
        $this->assertSame($time, $clock->now());
    }

    public function testTimezone(): void
    {
        $timezone = new DateTimeZone('Europe/Paris');
        $clock = new FakeClock(new \DateTimeImmutable('2016-01-01 12:30:00'));
        $timeWithTimezone = $clock->now($timezone);

        $this->assertSame(
            $timezone->getName(),
            $timeWithTimezone
                ->getTimezone()
                ->getName()
        );

        // Check that it's still the same universal time regardless timezone
        $timeWithDflTimezone = $clock->now();
        $this->assertNotEquals(
            $timeWithTimezone->getTimezone()->getName(),
            $timeWithDflTimezone->getTimezone()->getName()
        );
        $this->assertEquals($timeWithTimezone->format('U'), $timeWithDflTimezone->format('U'));
    }
}
