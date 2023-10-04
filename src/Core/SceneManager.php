<?php

namespace Blaj\PhpEngine\Core;

use Blaj\PhpEngine\Container\Attributes\Service;
use Blaj\PhpEngine\Graphics\Renderer;
use Blaj\PhpEngine\Graphics\Window;

#[Service]
class SceneManager
{
    private ?Scene $currentScene = null;

    public function __construct(Window $window, Renderer $renderer)
    {
        $this->currentScene = new Scene($window->width, $window->height, $renderer);
    }

    public function render(): void {
        if ($this->currentScene === null) {
            return;
        }

        $this->currentScene->render();
    }

    public function update(float $deltaTime): void {
        if ($this->currentScene === null) {
            return;
        }

        $this->currentScene->update($deltaTime);
    }

    public function getCurrentScene(): ?Scene
    {
        return $this->currentScene;
    }
}