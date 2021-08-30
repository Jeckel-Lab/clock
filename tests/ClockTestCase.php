<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 30/08/2021
 */

declare(strict_types=1);

namespace Tests\JeckelLab\Clock;

use DateTimeInterface;
use PHPUnit\Framework\TestCase;

/**
 *
 */
abstract class ClockTestCase extends TestCase
{
    /**
     * @var string
     */
    private $originalDefaultTimeZone;

    /**
     * Set system default timezone to UTC for the test
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->originalDefaultTimeZone = date_default_timezone_get();
        date_default_timezone_set('UTC');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        date_default_timezone_set($this->originalDefaultTimeZone);
    }

    /**
     * @param DateTimeInterface $expected
     * @param DateTimeInterface $actual
     */
    public function assertSameUniversalTime(DateTimeInterface $expected, DateTimeInterface $actual): void
    {
        $this->assertEquals($expected->format('U'), $actual->format('U'));
    }

    /**
     * @param DateTimeInterface $actual
     */
    public function assertHasDefaultTimeZone(DateTimeInterface $actual): void
    {
        $this->assertEquals(date_default_timezone_get(), $actual->getTimezone()->getName());
    }

    /**
     * @param DateTimeInterface $actual
     */
    public function assertHasNotDefaultTimeZone(DateTimeInterface $actual): void
    {
        $this->assertNotEquals(date_default_timezone_get(), $actual->getTimezone()->getName());
    }

    /**
     * @param \DateTimeZone $expected
     * @param \DateTimeZone $actual
     */
    public function assertEqualsTimeZone(\DateTimeZone $expected, \DateTimeZone $actual): void
    {
        $this->assertEquals($expected->getName(), $actual->getName());
    }

    /**
     * @param \DateTimeZone $timeZone
     */
    public function assertIsDefaultTimeZone(\DateTimeZone $timeZone): void
    {
        $this->assertEquals(date_default_timezone_get(), $timeZone->getName());
    }

    /**
     * @param \DateTimeZone     $expected
     * @param DateTimeInterface $actual
     */
    public function assertDateTimeHasTimeZone(\DateTimeZone $expected, \DateTimeInterface $actual): void
    {
        $this->assertEqualsTimeZone($expected, $actual->getTimezone());
    }
}
