<?php

namespace Blaj\PhpEngine\Container\Exception;

use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

class NotFoundException extends RuntimeException implements NotFoundExceptionInterface
{

}