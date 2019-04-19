<?php
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
