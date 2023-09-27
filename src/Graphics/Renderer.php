<?php

namespace Blaj\PhpEngine\Graphics;

use Blaj\PhpEngine\Core\GameObject;

class Renderer
{
    private static int $maxBatchSize = 1000;

    /**
     * @var array<BatchRenderer>
     */
    private array $batchRenderers = [];

    public function render(): void {
        array_walk($this->batchRenderers, fn(BatchRenderer $batchRenderer) => $batchRenderer->render());
    }

    public function addGameObject(GameObject $gameObject): void {
        $mesh = $gameObject->getComponent(Mesh::class);

        if ($mesh === null) {
            return;
        }

        $this->addMesh($mesh);
    }

    private function addMesh(Mesh $mesh): void {
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
            $newBatchRenderer->initialize();

            $this->batchRenderers[] = $newBatchRenderer;

            $newBatchRenderer->addMesh($mesh);
        }
    }
}