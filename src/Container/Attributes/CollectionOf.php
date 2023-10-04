<?php

namespace Blaj\PhpEngine\Container\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
class CollectionOf
{
    public function __construct(public readonly string $interface)
    {
    }
}