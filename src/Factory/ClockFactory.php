<?php
declare(strict_types=1);

namespace JeckelLab\Clock\Factory;

use DateTimeImmutable;
use DateTimeZone;
use Exception;
use JeckelLab\Clock\Clock\FakedClock;
use JeckelLab\Clock\Clock\FrozenClock;
use JeckelLab\Clock\Clock\RealClock;
use JeckelLab\Clock\Exception\InvalidFakeClockInitialValueException;
use JeckelLab\Clock\Exception\RuntimeException;
use JeckelLab\Contract\Infrastructure\System\Clock as ClockInterface;

/**
 * Class ClockFactory
 * @package Jeckel\Clock
 */
class ClockFactory
{
    /**
     * @param array $config
     * @return ClockInterface
     * @throws RuntimeException
     */
    public static function getClock(array $config = []): ClockInterface
    {
        try {
            $timezone = new DateTimeZone(
                (string) ($config['timezone'] ?? date_default_timezone_get())
            );
        } catch (Exception $e) {
            throw new RuntimeException(
                'Invalid timezone provided: ' . $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        }

        try {
            switch ($config['mode'] ?? '') {
                case 'frozen':
                    return new FrozenClock(self::getInitialTimeFromConfig($config, $timezone));
                case 'faked':
                    return new FakedClock(self::getInitialTimeFromConfig($config, $timezone));
                case 'real':
                default:
                    return new RealClock($timezone);
            }
        } catch (InvalidFakeClockInitialValueException $e) {
            if (isset($config['fallback_to_current_date']) && $config['fallback_to_current_date'] === true) {
                return new RealClock($timezone);
            }
            throw $e;
        }
    }

    /**
     * @param array $config
     * @return DateTimeImmutable
     * @throws RuntimeException
     */
    protected static function getInitialTimeFromConfig(array $config, DateTimeZone $timeZone): DateTimeImmutable
    {
        try {
            if (isset($config['fake_time_init'])) {
                return new DateTimeImmutable((string) $config['fake_time_init'], $timeZone);
            }
            if (isset($config['fake_time_path'])) {
                $filePath = (string) $config['fake_time_path'];
                if (! is_readable($filePath)) {
                    throw new InvalidFakeClockInitialValueException('Impossible to read fake time file: ' . $filePath);
                }
                return new DateTimeImmutable(file_get_contents($filePath), $timeZone);
            }
        } catch (Exception $e) {
            throw new InvalidFakeClockInitialValueException(
                'Invalid fake time provided: ' . $e->getMessage(),
                (int) $e->getCode(),
                $e
            );
        }
        throw new RuntimeException('Missing configuration options for fake time: fake_time_init or fake_time_path');
    }
}
