<?php
declare(strict_types=1);

namespace JeckelLab\Clock;

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use JeckelLab\Contract\Infrastructure\System\Clock as ClockInterface;

/**
 * Class Clock
 * @package Jeckel\Clock
 */
class Clock implements ClockInterface
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
     * @throws Exception
     */
    public function now(?DateTimeZone $timezone = null): DateTimeImmutable
    {
        return new DateTimeImmutable('now', $timezone ?: $this->timezone);
    }
}
