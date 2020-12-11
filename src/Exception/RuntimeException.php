<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 11/12/2020
 */

declare(strict_types=1);

namespace JeckelLab\Clock\Exception;

use JeckelLab\Contract\Infrastructure\Exception\InfrastructureException;
use RuntimeException as RuntimeExceptionCore;

/**
 * Class RuntimeException
 * @package JeckelLab\Clock\Exception
 * @psalm-immutable
 * @psalm-suppress MutableDependency
 */
class RuntimeException extends RuntimeExceptionCore implements InfrastructureException
{

}
