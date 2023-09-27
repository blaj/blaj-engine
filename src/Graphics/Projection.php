<?php

namespace Blaj\PhpEngine\Graphics;

use GL\Math\Mat4;

class Projection
{
    private static function fov(): float {
        return deg2rad(60.0);
    }

    private static float $zNear = 0.01;
    private static float $zFar = 1000.0;

    private Mat4 $projectionMatrix;

    public function __construct(int $width, int $height)
    {
        $this->projectionMatrix = new Mat4();
        $this->updateProjectionMatrix($width, $height);
    }

    public function getProjectionMatrix(): Mat4 {
        return $this->projectionMatrix;
    }

    public function updateProjectionMatrix(int $width, int $height) {
        $this->projectionMatrix->perspective(self::fov(), (float) $width/ $height, self::$zNear, self::$zFar);
    }
}