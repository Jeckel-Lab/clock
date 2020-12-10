<?php
declare(strict_types=1);

namespace JeckelLab\Clock;

use InvalidArgumentException;
use JeckelLab\Contract\Infrastructure\Exception\InfrastructureException;

/**
 * @psalm-immutable
 * @psalm-suppress MutableDependency
 */
class InvalidClockFileException extends InvalidArgumentException implements InfrastructureException
{

}
