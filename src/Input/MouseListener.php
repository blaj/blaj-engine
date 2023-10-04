<?php

declare(strict_types=1);

namespace Blaj\PhpEngine\Input;

use Blaj\PhpEngine\Container\Attributes\Service;

#[Service]
class MouseListener
{

    private float $x = 0.0;
    private float $y = 0.0;
    private float $lastX = 0.0;
    private float $lastY = 0.0;
    private float $scrollX = 0.0;
    private float $scrollY = 0.0;
    private bool $isDragging = false;

    /**
     * @var array<bool>
     */
    private array $buttonPressed = [];

    public function __construct()
    {
    }

    public function cursorPosCallback(float $x, float $y): void
    {
        $this->lastX = $this->x;
        $this->lastY = $this->y;

        $this->x = $x;
        $this->y = $y;

        $this->isDragging = $this->checkDragging(0) || $this->checkDragging(1) || $this->checkDragging(2);
    }

    public function mouseButtonCallback(int $button, int $action, int $mods): void
    {
        if ($action === GLFW_PRESS) {
            if ($button < count($this->buttonPressed)) {
                $this->buttonPressed[$button] = true;
            }
        } else if ($action === GLFW_RELEASE) {
            if ($button < count($this->buttonPressed)) {
                $this->buttonPressed[$button] = false;
                $this->isDragging = false;
            }
        }
    }

    public function mouseScrollCallback(float $xOffset, float $yOffset): void
    {
        $this->scrollX = $xOffset;
        $this->scrollY = $yOffset;
    }

    public function getX(): float
    {
        return $this->x;
    }

    public function getY(): float
    {
        return $this->y;
    }

    public function getDX(): float
    {
        return $this->lastX - $this->x;
    }

    public function getDY(): float
    {
        return $this->lastY - $this->x;
    }

    public function getScrollX(): float
    {
        return $this->scrollX;
    }

    public function getScrollY(): float
    {
        return $this->scrollY;
    }

    public function isDragging(): bool
    {
        return $this->isDragging;
    }

    public function isButtonDown(int $button): bool
    {
        if ($button < count($this->buttonPressed)) {
            return $this->buttonPressed[$button];
        } else {
            return false;
        }
    }

    private function checkDragging(int $index): bool
    {
        return array_key_exists($index, $this->buttonPressed) && $this->buttonPressed[$index];
    }
}