<?php

declare(strict_types=1);

namespace Blaj\PhpEngine\Graphics;

use Blaj\PhpEngine\Core\GameObject;
use GL\Math\GLM;
use GL\Math\Mat4;
use GL\Math\Vec3;

class Camera extends GameObject
{
    private static function fov(): float
    {
        return GLM::radians(70.0);
    }

    private static float $zNear = 0.01;
    private static float $zFar = 1000.0;

    private Mat4 $projectionMatrix;
    private Mat4 $viewMatrix;
    private Vec3 $position;

    public function __construct(int $width, int $height)
    {
        parent::__construct('Camera');

        $this->projectionMatrix = new Mat4();
        $this->viewMatrix = new Mat4();
        $this->position = new Vec3();

        $this->updateProjectionMatrix($width, $height);
    }

    public function updateProjectionMatrix(int $width, int $height): void
    {
        $this->projectionMatrix->perspective(self::fov(), (float)$width / $height, self::$zNear, self::$zFar);
    }

    public function getProjectionMatrix(): Mat4
    {
        return $this->projectionMatrix;
    }

    public function getViewMatrix(): Mat4
    {
        $cameraFront = new Vec3(0.0, 0.0, 0.0);
        $cameraUp = new Vec3(0.0, 1.0, 0.0);

        $this->viewMatrix
            ->lookAt(
                $this->position,
                $cameraFront,
                $cameraUp);
        return $this->viewMatrix;
    }

    public function getPosition(): Vec3
    {
        return $this->position;
    }

    public function setPosition(Vec3 $position): self
    {
        $this->position = $position;
        return $this;
    }
}