<?php

declare(strict_types=1);

namespace Blaj\PhpEngine\Graphics;

use Blaj\PhpEngine\Container\Attributes\Service;
use Blaj\PhpEngine\Core\GameObject;
use GL\Math\Mat4;

#[Service]
class Renderer
{
    private static int $maxBatchSize = 1000;

    /**
     * @var array<BatchRenderer>
     */
    private array $batchRenderers = [];

    public function render(Mat4 $projectionMatrix, Mat4 $viewMatrix): void
    {
        array_walk($this->batchRenderers, fn(BatchRenderer $batchRenderer) => $batchRenderer->render($projectionMatrix, $viewMatrix));
    }

    public function addGameObject(GameObject $gameObject): void
    {
        $mesh = $gameObject->getComponent(Mesh::class);

        if ($mesh === null) {
            return;
        }

        $this->addMesh($mesh);
    }

    private function addMesh(Mesh $mesh): void
    {
        $added = false;

        foreach ($this->batchRenderers as $batchRenderer) {
            if ($batchRenderer->hasRoom()) {
                $batchRenderer->addMesh($mesh);
                $added = true;
                break;
            }
        }

        if (!$added) {
            $newBatchRenderer = new BatchRenderer(self::$maxBatchSize);
            $this->batchRenderers[] = $newBatchRenderer;
            $newBatchRenderer->addMesh($mesh);
        }
    }
}