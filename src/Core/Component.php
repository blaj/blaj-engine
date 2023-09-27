<?php

declare(strict_types=1);

namespace Blaj\PhpEngine\Core;

abstract class Component
{
    abstract function render(): void;

    abstract function update(float $deltaTime): void;

    public function initialize(): void
    {
    }

    public function destroy(): void
    {
    }
}