<?php

declare(strict_types=1);

namespace Tests\JeckelLab\Clock\Factory;

use Exception;
use JeckelLab\Clock\Clock\FakedClock;
use JeckelLab\Clock\Clock\FrozenClock;
use JeckelLab\Clock\Clock\RealClock;
use JeckelLab\Clock\Factory\ClockFactory;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

/**
 * Class ClockFactoryTest
 * @package Test\Jeckel\Clock
 */
final class ClockFactoryTest extends TestCase
{
    public function testGetClock(): void
    {
        $this->assertInstanceOf(RealClock::class, ClockFactory::getClock());
    }

    public function testGetFrozenClock(): void
    {
        $clock = ClockFactory::getClock(['mode' => 'frozen', 'fake_time_init' => '2018-12-01 06:00:00']);
        $this->assertInstanceOf(FrozenClock::class, $clock);

        $time = $clock->now();
        $this->assertEquals('2018-12-01 06:00:00', $time->format('Y-m-d H:i:s'));
    }

    public function testGetFakedClock(): void
    {
        $clock = ClockFactory::getClock(['mode' => 'faked', 'fake_time_init' => '2018-12-01 06:00:00']);
        $this->assertInstanceOf(FakedClock::class, $clock);

        sleep(1);
        $time = $clock->now();
        $this->assertEquals('2018-12-01 06:00:01', $time->format('Y-m-d H:i:s'));
    }

    public function testGetFakedClockWithTimeZone(): void
    {
        $clock = ClockFactory::getClock(
            [
                'mode'           => 'faked',
                'fake_time_init' => '2018-12-01 06:00:00',
                'timezone'       => 'GMT+2'
            ]
        );
        $this->assertInstanceOf(FakedClock::class, $clock);

        sleep(1);
        $time = $clock->now();
        $this->assertEquals('2018-12-01 08:00:01', $time->format('Y-m-d H:i:s'));
    }

    /**
     * @throws Exception
     */
    public function testGetFrozenClockFromFile(): void
    {
        $frozenTime = "2018-02-01 10:30:15";
        $root = vfsStream::setup('root', 444, ['clock' => $frozenTime]);
        $clock = ClockFactory::getClock(
            [
                'mode' => 'frozen',
                'fake_time_path' => $root->url() . '/clock'
            ]
        );
        $this->assertEquals($frozenTime, $clock->now()->format('Y-m-d H:i:s'));
    }
}
