<?php

namespace Blaj\PhpEngine\Core;

class SceneManager
{
    private ?Scene $currentScene = null;

    public function __construct(int $width, int $height)
    {
        $this->currentScene = new Scene($width, $height);
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
}