<?php

namespace Screenfeed\AdminbarTools\Dependencies\League\Container\Exception;

use Screenfeed\AdminbarTools\Dependencies\Interop\Container\Exception\NotFoundException as NotFoundExceptionInterface;
use InvalidArgumentException;

class NotFoundException extends InvalidArgumentException implements NotFoundExceptionInterface
{
}
