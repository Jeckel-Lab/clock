<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 25/03/2021
 */

declare(strict_types=1);

namespace JeckelLab\Clock\CodeceptionHelper;

use Codeception\Configuration;
use Codeception\Module;
use Codeception\TestInterface;
use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;
use RuntimeException;

/**
 * Class Clock
 * @package JeckelLab\Clock\CodeceptionHelper
 */
class Clock extends Module
{
    /**
     * @var string[]
     */
    protected $config = [
        'date_format' => 'Y/m/d',
        'time_format' => 'H:i:s'
    ];

    /**
     * @var array
     */
    protected $requiredFields = ['fake_time_path', 'date_format', 'time_format'];

    // @codingStandardsIgnoreStart
    /**
     * HOOK: before test
     * @param TestInterface $test
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function _before(TestInterface $test): void
    {
        $this->haveCurrentDateTime(new DateTimeImmutable());
    }
    // @codingStandardsIgnoreEnd

    /**
     * @Given current date is :currentDate and time is :currentTime
     * @param string $date
     * @param string $time
     */
    public function haveCurrentDateAndTime(string $date, string $time): void
    {
        $format = sprintf('%s %s', $this->config['date_format'], $this->config['time_format']);
        $currentDate = DateTimeImmutable::createFromFormat($format, sprintf('%s %s', $date, $time));
        if (false === $currentDate) {
            throw new InvalidArgumentException('Invalid date / time format');
        }
        $this->haveCurrentDateTime($currentDate);
    }

    /**
     * @param DateTimeInterface $dateTime
     */
    public function haveCurrentDateTime(DateTimeInterface $dateTime): void
    {
        $fullPath = Configuration::projectDir() . $this->config['fake_time_path'];

        $dir = dirname($fullPath);
        if (!is_dir($dir) && !mkdir($dir) && !is_dir($dir)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $dir));
        }
        file_put_contents($fullPath, $dateTime->format('Y-m-d H:i:s'));
    }
}
