<?php

declare(strict_types=1);

namespace Blaj\PhpEngine\Core;

use Blaj\PhpEngine\Graphics\Camera;
use Blaj\PhpEngine\Graphics\Mesh;
use Blaj\PhpEngine\Graphics\Projection;
use Blaj\PhpEngine\Graphics\Renderer;
use Blaj\PhpEngine\Graphics\Shader;
use GL\Buffer\FloatBuffer;
use GL\Math\Vec3;

class Scene
{
    private Camera $camera;

    private Renderer $renderer;

    private Projection $projection;

    /**
     * @var array<GameObject>
     */
    private array $gameObjects = [];

    public function __construct(int $width, int $height)
    {
        $this->projection = new Projection($width, $height);
        $this->renderer = new Renderer();
        $this->camera = new Camera(new Vec3(0, 0, -200));

        $gameObject = new GameObject(name: 'go');
        $gameObject->addComponent(new Mesh([
            -10.0, -10.0,  10.0, 1.0, 0.0, 0.0,
            10.0, -10.0,  10.0, 0.0, 0.0, 1.0,
            10.0,  10.0,  10.0, 0.0, 0.0, 1.0,
            -10.0,  10.0,  10.0, 0.0, 0.0, 1.0
        ], $this->projection->getProjectionMatrix(), $this->camera->getViewMatrix()));

        $this->addGameObject($gameObject);
    }

    public function initialize(): void {
        array_walk($this->gameObjects, function (GameObject $gameObject) {
            $gameObject->initialize();
            $this->renderer->addGameObject($gameObject);
        });
    }

    public function render(): void
    {
        array_walk($this->gameObjects, fn(GameObject $gameObject) => $gameObject->render());
    }

    public function update(float $deltaTime): void
    {
        array_walk($this->gameObjects, fn(GameObject $gameObject) => $gameObject->update($deltaTime));
    }

    public function addGameObject(GameObject $gameObject): void
    {
        $this->gameObjects[] = $gameObject;
        $this->renderer->addGameObject($gameObject);
        $gameObject->initialize();
    }
}
