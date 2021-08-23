<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 19/04/2019
 */

declare(strict_types=1);

namespace JeckelLab\Clock\Clock;

use DateTimeImmutable;
use DateTimeZone;
use JeckelLab\Contract\Infrastructure\System\Clock as ClockInterface;

/**
 * Class FakeClock
 * @package Jeckel\Clock
 */
class FrozenClock implements ClockInterface
{
    /**
     * @var DateTimeImmutable
     */
    protected $now;

    /**
     * FakeClock constructor.
     * @param DateTimeImmutable $now
     * @param DateTimeZone|null $timezone
     */
    public function __construct(DateTimeImmutable $now, ?DateTimeZone $timezone = null)
    {
        $initialDatetime = $now;

        if (null !== $timezone) {
            $initialDatetime = $now->setTimezone($timezone);
        }

        $this->now = $initialDatetime;
    }

    /**
     * @param DateTimeImmutable $now
     */
    public function setClock(DateTimeImmutable $now): void
    {
        $this->now = $now;
    }

    /**
     * @param DateTimeZone|null $timezone
     * @return DateTimeImmutable
     */
    public function now(?DateTimeZone $timezone = null): DateTimeImmutable
    {
        if (null !== $timezone) {
            return $this->now->setTimezone($timezone);
        }
        return $this->now;
    }
}
