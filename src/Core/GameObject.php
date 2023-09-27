<?php

declare(strict_types=1);

namespace Blaj\PhpEngine\Core;

use RuntimeException;

class GameObject
{
    public function __construct(
        private string $name,
        private Transform $transform = new Transform(),
        private array $components = [])
    {
    }

    public function render(): void
    {
        array_walk($this->components, fn(Component $component) => $component->render());
    }

    public function update(float $deltaTime): void
    {
        array_walk($this->components, fn(Component $component) => $component->update($deltaTime));
    }

    public function initialize(): void
    {
        array_walk($this->components, fn(Component $component) => $component->initialize());
    }

    public function destroy(): void
    {
        array_walk($this->components, fn(Component $component) => $component->destroy());
    }

    public function addComponent(Component $component): void
    {
        if ($this->hasComponent($component)) {
            throw new RuntimeException('GameObject can only have one component of a given type!');
        }

        $component->initialize();;
        $this->components[self::getComponentIndex($component)] = $component;
    }

    public function removeComponent(Component $component): void
    {
        if (!$this->hasComponent($component)) {
            throw new RuntimeException('GameObject not have that component!');
        }

        $component->destroy();
        unset($this->components[self::getComponentIndex($component)]);
    }

    public function getComponent(string $componentClass): ?Component
    {
        return array_key_exists($componentClass, $this->components) ? $this->components[$componentClass] : null;
    }

    public function hasComponent(Component $component): bool
    {
        return array_key_exists(self::getComponentIndex($component), $this->components);
    }

    private static function getComponentIndex(Component $component): string
    {
        return get_class($component);
    }
}
