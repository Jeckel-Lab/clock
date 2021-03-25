<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 25/03/2021
 */

declare(strict_types=1);

namespace JeckelLab\Clock\CodeceptionHelper;

use Codeception\Configuration;
use Codeception\Module;
use RuntimeException;

/**
 * Class Clock
 * @package JeckelLab\Clock\CodeceptionHelper
 */
class Clock extends Module
{
    /**
     * @var array
     */
    protected $requiredFields = ['fake_time_path'];

    /**
     * Expected format 'Y-m-d H:i:s'
     * @Given current date and time is :currentDate
     * @param string $currentDate
     */
    public function currentDateAndTimeIs(string $currentDate): void
    {
        /** @psalm-suppress MixedArrayAccess */
        $fullPath = Configuration::projectDir() . ((string) $this->config['fake_time_path']);

        $dir = dirname($fullPath);
        if (!is_dir($dir) && !mkdir($dir) && !is_dir($dir)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $dir));
        }
        file_put_contents($fullPath, $currentDate);
    }
}
