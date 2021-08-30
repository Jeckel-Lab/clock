<?php

declare(strict_types=1);

namespace Tests\JeckelLab\Clock\Factory;

use Exception;
use JeckelLab\Clock\Clock\FakedClock;
use JeckelLab\Clock\Clock\FrozenClock;
use JeckelLab\Clock\Clock\RealClock;
use JeckelLab\Clock\Exception\RuntimeException;
use JeckelLab\Clock\Factory\ClockFactory;
use org\bovigo\vfs\vfsStream;
use Tests\JeckelLab\Clock\ClockTestCase;

/**
 * Class ClockFactoryTest
 * @package Test\Jeckel\Clock
 */
final class ClockFactoryTest extends ClockTestCase
{

    /***** Real Clock Factory *****/

    public function testGetDefaultClockIsRealClock(): void
    {
        $this->assertInstanceOf(
            RealClock::class,
            ClockFactory::getClock()
        );
    }

    public function testGetRealClock(): void
    {
        $this->assertInstanceOf(
            RealClock::class,
            ClockFactory::getClock(['mode' => 'real'])
        );
    }

    /***** Frozen Clock Factory *****/

    public function testGetFrozenClock(): void
    {
        $clock = ClockFactory::getClock([
            'mode' => 'frozen',
            'fake_time_init' => '2018-12-01 06:00:00'
        ]);
        $this->assertInstanceOf(FrozenClock::class, $clock);

        $now = $clock->now();
        $this->assertEquals('2018-12-01 06:00:00', $now->format('Y-m-d H:i:s'));
        $this->assertHasDefaultTimeZone($now);
    }

    public function testGetFrozenClockWithTimeZone(): void
    {
        $expectedTimeZone = new \DateTimeZone('Europe/Paris');

        $clock = ClockFactory::getClock([
            'mode' => 'frozen',
            'fake_time_init' => '2018-12-01 06:00:00',
            'timezone' => 'Europe/Paris'
        ]);
        $this->assertInstanceOf(FrozenClock::class, $clock);

        $now = $clock->now();
        $this->assertEqualsTimeZone($expectedTimeZone, $clock->getTimeZone());
        $this->assertEquals('2018-12-01 06:00:00', $now->format('Y-m-d H:i:s'));
        $this->assertDateTimeHasTimeZone($expectedTimeZone, $now);
    }

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
        $this->assertInstanceOf(FrozenClock::class, $clock);

        $now = $clock->now();
        $this->assertEquals($frozenTime, $now->format('Y-m-d H:i:s'));
        $this->assertHasDefaultTimeZone($now);
    }

    public function testGetFrozenClockFromFileWithTimeZone(): void
    {
        $expectedTimeZone = new \DateTimeZone('Europe/Paris');
        $frozenTime = "2018-02-01 10:30:15";
        $root = vfsStream::setup('root', 444, ['clock' => $frozenTime]);
        $clock = ClockFactory::getClock(
            [
                'mode' => 'frozen',
                'fake_time_path' => $root->url() . '/clock',
                'timezone' => 'Europe/Paris'
            ]
        );
        $this->assertInstanceOf(FrozenClock::class, $clock);
        $this->assertEqualsTimeZone($expectedTimeZone, $clock->getTimeZone());

        $now = $clock->now();
        $this->assertEquals($frozenTime, $now->format('Y-m-d H:i:s'));
        $this->assertDateTimeHasTimeZone($expectedTimeZone, $now);
    }

    /***** Faked Clock Factory *****/

    public function testGetFakedClock(): void
    {
        $clock = ClockFactory::getClock([
            'mode' => 'faked',
            'fake_time_init' => '2018-12-01 06:00:00'
        ]);
        $this->assertInstanceOf(FakedClock::class, $clock);
        $this->assertIsDefaultTimeZone($clock->getTimeZone());

        sleep(1);
        $now = $clock->now();
        $this->assertEquals('2018-12-01 06:00:01', $now->format('Y-m-d H:i:s'));
        $this->assertHasDefaultTimeZone($now);
    }

    public function testGetFakedClockWithTimeZone(): void
    {
        $expectedTimeZone = new \DateTimeZone('Europe/Paris');
        $clock = ClockFactory::getClock(
            [
                'mode'           => 'faked',
                'fake_time_init' => '2018-12-01 06:00:00',
                'timezone'       => 'Europe/Paris'
            ]
        );
        $this->assertInstanceOf(FakedClock::class, $clock);
        $this->assertEqualsTimeZone($expectedTimeZone, $clock->getTimeZone());

        sleep(1);
        $now = $clock->now();
        $this->assertEquals('2018-12-01 06:00:01', $now->format('Y-m-d H:i:s'));
        $this->assertDateTimeHasTimeZone($expectedTimeZone, $now);
    }

    public function testGetFakedClockFromFile(): void
    {
        $fakeTimeInit = "2018-02-01 10:30:15";
        $root = vfsStream::setup('root', 444, ['clock' => $fakeTimeInit]);
        $clock = ClockFactory::getClock(
            [
                'mode' => 'faked',
                'fake_time_path' => $root->url() . '/clock'
            ]
        );
        $this->assertInstanceOf(FakedClock::class, $clock);

        sleep(1);
        $now = $clock->now();
        $this->assertEquals("2018-02-01 10:30:16", $now->format('Y-m-d H:i:s'));
        $this->assertHasDefaultTimeZone($now);
    }

    public function testGetFakedClockFromFileWithTimeZone(): void
    {
        $expectedTimeZone = new \DateTimeZone('Europe/Paris');
        $fakeTimeInit = "2018-02-01 10:30:15";
        $root = vfsStream::setup('root', 444, ['clock' => $fakeTimeInit]);
        $clock = ClockFactory::getClock(
            [
                'mode' => 'faked',
                'fake_time_path' => $root->url() . '/clock',
                'timezone' => 'Europe/Paris'
            ]
        );
        $this->assertInstanceOf(FakedClock::class, $clock);
        $this->assertEqualsTimeZone($expectedTimeZone, $clock->getTimeZone());

        sleep(1);
        $now = $clock->now();
        $this->assertEquals("2018-02-01 10:30:16", $now->format('Y-m-d H:i:s'));
        $this->assertDateTimeHasTimeZone($expectedTimeZone, $now);
    }


    /***** Test error cases *****/

    public function testGetClockWithInvalidTimezoneFailed(): void
    {
        $this->expectException(RuntimeException::class);
        ClockFactory::getClock(['timezone' => 'foobarbaz']);
    }

    public function testGetFrozenClockWithoutInitialValueFailed(): void
    {
        $this->expectException(RuntimeException::class);
        ClockFactory::getClock(['mode' => 'frozen']);
    }

    public function testGetFakedClockWithoutInitialValueFailed(): void
    {
        $this->expectException(RuntimeException::class);
        ClockFactory::getClock(['mode' => 'faked']);
    }

    public function testGetFrozenClockWithInvalidInitialValueFailed(): void
    {
        $this->expectException(RuntimeException::class);
        ClockFactory::getClock(['mode' => 'frozen', 'fake_time_init' => 'foobarbaz']);
    }

    public function testGetFakedClockWithInvalidInitialValueFailed(): void
    {
        $this->expectException(RuntimeException::class);
        ClockFactory::getClock(['mode' => 'faked', 'fake_time_init' => 'foobarbaz']);
    }

    public function testGetFrozenClockWithUnreadableFile(): void
    {
        $this->expectException(RuntimeException::class);
        ClockFactory::getClock(['mode' => 'frozen', 'fake_time_path' => '/foo/bar/baz']);
    }

    public function testGetFakedClockWithUnreadableFile(): void
    {
        $this->expectException(RuntimeException::class);
        ClockFactory::getClock(['mode' => 'faked', 'fake_time_path' => '/foo/bar/baz']);
    }

    public function testGetFrozenClockWithUnreadableFileAndFallbackToCurrentDate(): void
    {
        self::assertInstanceOf(
            RealClock::class,
            ClockFactory::getClock(
                [
                    'mode' => 'frozen',
                    'fake_time_path' => '/foo/bar/baz',
                    'fallback_to_current_date' => true
                ]
            )
        );
    }

    public function testGetFakedClockWithUnreadableFileAndFallbackToCurrentDate(): void
    {
        self::assertInstanceOf(
            RealClock::class,
            ClockFactory::getClock(
                [
                    'mode' => 'faked',
                    'fake_time_path' => '/foo/bar/baz',
                    'fallback_to_current_date' => true
                ]
            )
        );
    }

    public function testGetFrozenClockWithInvalidInitFileContent(): void
    {
        $root = vfsStream::setup('root', 444, ['clock' => 'foobarbaz']);
        $this->expectException(RuntimeException::class);
        ClockFactory::getClock(['mode' => 'frozen', 'fake_time_path' => $root->url() . '/clock']);
    }

    public function testGetFakedClockWithInvalidInitFileContent(): void
    {
        $root = vfsStream::setup('root', 444, ['clock' => 'foobarbaz']);
        $this->expectException(RuntimeException::class);
        ClockFactory::getClock(['mode' => 'faked', 'fake_time_path' => $root->url() . '/clock']);
    }
}
