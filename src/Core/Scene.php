<?php

declare(strict_types=1);

namespace Blaj\PhpEngine\Core;

use Blaj\PhpEngine\Graphics\Camera;
use Blaj\PhpEngine\Graphics\Mesh;
use Blaj\PhpEngine\Graphics\Renderer;
use GL\Math\Vec3;

class Scene
{
    private Renderer $renderer;

    /**
     * @var array<GameObject>
     */
    private array $gameObjects = [];

    public function __construct(int $width, int $height, Renderer $renderer)
    {
        $this->renderer = $renderer;

        $camera = new Camera($width, $height);
        $camera->setPosition(new Vec3(0.0, 0.0, -20.0));
        $this->addGameObject($camera);

        for ($i = 0; $i < 1000; $i++) {
            $gameObject = new GameObject(name: 'go' . $i);
            $gameObject->addComponent(
                new Mesh(
                    [
                        10.5, -10.5, 0.0, 1.0, 0.0, 0.0, 1.0,  // bottom right
                        -10.5, -10.5, 0.0, 0.0, 1.0, 0.0, 1.0,  // bottom let
                        10.0, 10.5, 0.0, 0.0, 0.0, 1.0, 1.0// top
                    ]));
            $gameObject->addComponent(new Transform(new Vec3(0.0, 0.0, 0.0), new Vec3(1.0, 1.0, 1.0)));

            $this->addGameObject($gameObject);
        }
    }

    public function initialize(): void
    {
        array_walk($this->gameObjects, function (GameObject $gameObject) {
            $gameObject->initialize();

            if ($gameObject instanceof Mesh) {
                $this->renderer->addGameObject($gameObject);
            }
        });
    }

    public function render(): void
    {
        $camera = $this->getGameObject(Camera::class);
        $this->renderer->render($camera->getProjectionMatrix(), $camera->getViewMatrix());
    }

    public function update(float $deltaTime): void
    {
        array_walk($this->gameObjects, fn(GameObject $gameObject) => $gameObject->update($deltaTime));
    }

    public function addGameObject(GameObject $gameObject): void
    {
        $this->gameObjects[$gameObject::class] = $gameObject;
        $this->renderer->addGameObject($gameObject);
        $gameObject->initialize();
    }

    public function getGameObject(string $gameObjectClass): ?GameObject
    {
        return array_key_exists($gameObjectClass, $this->gameObjects) ? $this->gameObjects[$gameObjectClass] : null;
    }

    public function hasGameObject(GameObject $gameObject): bool
    {
        return array_key_exists(self::getGameObjectIndex($gameObject), $this->gameObjects);
    }

    private static function getGameObjectIndex(GameObject $gameObject): string
    {
        return get_class($gameObject);
    }
}
