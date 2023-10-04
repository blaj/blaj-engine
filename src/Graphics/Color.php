<?php

namespace Blaj\PhpEngine\Graphics;

use GL\Math\Vec4;

class Color extends Vec4
{
    public function __construct(float $r, float $g, float $b, float $a)
    {
        parent::__construct($r, $g, $b, $a);
    }
}