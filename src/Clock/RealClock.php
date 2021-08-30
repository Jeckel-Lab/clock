<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 19/04/2019
 */

declare(strict_types=1);

namespace JeckelLab\Clock\Clock;

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use JeckelLab\Clock\Exception\RuntimeException;
use JeckelLab\Contract\Infrastructure\System\Clock as ClockInterface;

/**
 * Class Clock
 * @package Jeckel\Clock
 */
class RealClock implements ClockInterface
{
    /**
     * @var DateTimeZone
     */
    private $timezone;

    /**
     * Clock constructor.
     * @param DateTimeZone|null $timezone
     */
    public function __construct(?DateTimeZone $timezone = null)
    {
        $this->timezone = $timezone ?: new DateTimeZone(date_default_timezone_get());
    }

    /**
     * @param DateTimeZone|null $timezone
     * @return DateTimeImmutable
     * @throws RuntimeException
     */
    public function now(?DateTimeZone $timezone = null): DateTimeImmutable
    {
        try {
            return new DateTimeImmutable('now', $timezone ?: $this->timezone);
            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            throw new RuntimeException(
                'Error creating datetime: ' . $e->getMessage(),
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
