<?php
declare(strict_types=1);

namespace Test\Jeckel\Clock;

use DateTimeImmutable;
use Jeckel\Clock\Clock;
use Jeckel\Clock\ClockFactory;
use Jeckel\Clock\FakeClock;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamWrapper;
use PHPUnit\Framework\TestCase;

/**
 * Class ClockFactoryTest
 * @package Test\Jeckel\Clock
 */
final class ClockFactoryTest extends TestCase
{
    /**
     * @test getClock
     */
    public function testGetClock()
    {
        $this->assertInstanceOf(Clock::class, (new ClockFactory())->getClock());
    }

    /**
     * @test getClock
     * @throws \Exception
     */
    public function testGetFakeClock()
    {
        $now = new DateTimeImmutable('now');

        $clock = (new ClockFactory())->getClock(true);
        $this->assertInstanceOf(FakeClock::class, $clock);

        $time = $clock->now();

        $this->assertGreaterThanOrEqual($now, $time);
        $this->assertLessThanOrEqual(new DateTimeImmutable('now'), $time);
    }

    /**
     * @test getClock
     * @throws \Exception
     */
    public function testGetFakeClockFromFile()
    {
        $frozentime = "2018-02-01 10:30:15";
        $root = vfsStream::setup('root', 444, ['clock' => $frozentime]);
        $clock = (new ClockFactory())->getClock(true, $root->url() . '/clock');
        $this->assertEquals($frozentime, $clock->now()->format("Y-m-d h:i:s"));
    }
}
