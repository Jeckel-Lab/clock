<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 11/12/2020
 */

declare(strict_types=1);

namespace JeckelLab\Clock\Clock;

use DateInterval;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use JeckelLab\Clock\Exception\RuntimeException;
use JeckelLab\Contract\Infrastructure\System\Clock;

/**
 * Class FakeRunningClock
 * @package JeckelLab\Clock
 */
class FakedClock implements Clock
{
    /** @var DateTimeImmutable */
    protected $initialDatetime;

    /** @var DateTimeZone */
    private $timezone;

    /** @var DateInterval */
    private $diff;

    /**
     * Clock constructor.
     * @param DateTimeImmutable $initialDatetime
     * @param DateTimeZone|null $timezone
     * @throws RuntimeException
     */
    public function __construct(DateTimeImmutable $initialDatetime, ?DateTimeZone $timezone = null)
    {
        $this->timezone = $timezone ?: $initialDatetime->getTimezone();

        if ($initialDatetime->getTimezone()->getName() !== $this->timezone->getName()) {
            $initialDatetime = $initialDatetime->setTimezone($this->timezone);
        }

        $this->initialDatetime = $initialDatetime;
        try {
            $this->diff = (new DateTimeImmutable('now', $this->timezone))->diff($initialDatetime);
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new RuntimeException(
                'Error creating diff to fake clock: ' . $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param DateTimeZone|null $timezone
     * @return DateTimeImmutable
     * @throws RuntimeException
     */
    public function now(?DateTimeZone $timezone = null): DateTimeImmutable
    {
        try {
            return (new DateTimeImmutable('now', $timezone ?: $this->timezone))->add($this->diff);
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new RuntimeException(
                'Error creating current time: ' . $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @return DateTimeZone
     */
    public function getTimeZone(): DateTimeZone
    {
        return $this->timezone;
    }
}
