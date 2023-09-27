<?php

declare(strict_types=1);

namespace Blaj\PhpEngine\Graphics;

use GL\Math\Mat4;
use GL\Math\Vec3;

class Camera
{
    private Mat4 $projectionMatrix;
    private Mat4 $viewMatrix;
    private Vec3 $position;

    public function __construct(Vec3 $position)
    {
        $this->projectionMatrix = new Mat4();
        $this->viewMatrix = new Mat4();
        $this->position = $position;

        $this->projectionMatrix->perspective(70.0, 600 / 400, 0.01, 1000.0);
    }

    public function getProjectionMatrix(): Mat4
    {
        return $this->projectionMatrix;
    }

    public function getViewMatrix(): Mat4
    {
        $cameraFront = new Vec3(0.0, 0.0, -1.0);
        $cameraUp = new Vec3(0.0, 1.0, 0.0);

        $this->viewMatrix
            ->lookAt(
                new Vec3($this->position->x, $this->position->y,  $this->position->z),
                $cameraFront,
                $cameraUp);
        return $this->viewMatrix;
    }

    public function getPosition(): Vec3 {
        return $this->position;
    }
}