<?php

namespace Jeckel\Clock;

use DateTimeImmutable;
use Exception;

/**
 * Class ClockFactory
 * @package Jeckel\Clock
 */
class ClockFactory
{
    /**
     * @param bool   $frozen_clock
     * @param string $frozen_clock_file
     * @return ClockInterface
     * @throws Exception
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function getClock(bool $frozen_clock = false, string $frozen_clock_file = ''): ClockInterface
    {
        if ($frozen_clock) {
            if (is_readable($frozen_clock_file)) {
                $clock = file_get_contents($frozen_clock_file);
            }
            if (empty($clock)) {
                $clock = 'now';
            }

            return new FakeClock(new DateTimeImmutable($clock));
        }
        return new Clock();
    }
}
