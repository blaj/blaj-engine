<?php

declare(strict_types=1);

namespace Blaj\PhpEngine\Core;

abstract class Component
{
    private GameObject $gameObject;

    public function render(): void
    {
    }

    public function update(float $deltaTime): void
    {
    }

    public function initialize(): void
    {
    }

    public function destroy(): void
    {
    }

    public function getGameObject(): GameObject
    {
        return $this->gameObject;
    }

    public function setGameObject(GameObject $gameObject): self
    {
        $this->gameObject = $gameObject;
        return $this;
    }
}