<?php
declare(strict_types=1);

namespace Jeckel\Clock;

/**
 * Interface ClockInterface
 */
interface ClockInterface
{
    /**
     * @return \DateTimeImmutable
     */
    public function now(): \DateTimeImmutable;
}
