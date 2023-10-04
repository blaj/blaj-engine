<?php

namespace Blaj\PhpEngine\Game;

use Blaj\PhpEngine\Utils\Vec3Utils;
use GL\Math\Vec3;

class PlanetFace
{
    private Vec3 $localUp;
    private Vec3 $axisA;
    private Vec3 $axisB;

    public function __construct(Vec3 $localUp)
    {
        $this->localUp = $localUp;
        $this->axisA = new Vec3($localUp->y, $localUp->z, $localUp->x);
        $this->axisB = Vec3Utils::cross($localUp, $this->axisA);
    }
}