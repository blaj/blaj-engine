<?php

declare(strict_types=1);

namespace Blaj\PhpEngine\Input;

use Blaj\PhpEngine\Container\Attributes\Service;

#[Service]
class KeyboardListener
{

    /**
     * @var array<bool>
     */
    private array $keyPressed = [];

    public function keyCallback(int $key, int $scancode, int $action, int $mods): void
    {
        if ($action === GLFW_PRESS) {
            $this->keyPressed[$key] = true;
        } else if ($action === GLFW_RELEASE) {
            $this->keyPressed[$key] = false;
        }
    }

    public function isKeyPressed(int $key): bool
    {
        return array_key_exists($key, $this->keyPressed) && $this->keyPressed[$key];
    }
}