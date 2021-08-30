<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 19/04/2019
 */

declare(strict_types=1);

namespace JeckelLab\Clock\Clock;

use DateTimeImmutable;
use DateTimeZone;
use JeckelLab\Clock\Exception\InvalidFakeClockInitialValueException;
use JeckelLab\Clock\Exception\RuntimeException;
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

    /** @var DateTimeZone */
    private $timezone;

    /**
     * FakeClock constructor.
     * @param DateTimeImmutable $initialDatetime
     * @param DateTimeZone|null $timezone
     */
    public function __construct(DateTimeImmutable $initialDatetime, ?DateTimeZone $timezone = null)
    {
        $this->timezone = $timezone ?: $initialDatetime->getTimezone();

        if ($initialDatetime->getTimezone()->getName() !== $this->timezone->getName()) {
            $fixedDateTime = $initialDatetime->setTimezone($this->timezone);
            if (! $fixedDateTime instanceof DateTimeImmutable) {
                throw new RuntimeException('Error setting timezone');
            }
            $initialDatetime = $fixedDateTime;
        }

        $this->now = $initialDatetime;
    }

    /**
     * @param DateTimeImmutable $now
     */
    public function setClock(DateTimeImmutable $now): void
    {
        $newNow = DateTimeImmutable::createFromFormat('U', $now->format('U'));
        if (! $newNow instanceof DateTimeImmutable) {
            throw new RuntimeException('Error creating new date');
        }

        $this->now = $newNow->setTimezone($this->timezone);
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

    /**
     * @return DateTimeZone
     */
    public function getTimeZone(): DateTimeZone
    {
        return $this->timezone;
    }
}
