<?php

declare(strict_types=1);

namespace Blaj\PhpEngine\Graphics;

use Blaj\PhpEngine\Core\Transform;
use GL\Buffer\FloatBuffer;
use GL\Math\Mat4;

class BatchRenderer
{

    private static int $posSize = 3;
    private static int $colorSize = 4;
    private static int $posOffset = 0;
    private static int $colorOffset = 3 * GL_SIZEOF_FLOAT;
    private static int $vertexSize = 7;
    private static int $vertexSizeBytes = 7 * GL_SIZEOF_FLOAT;

    /**
     * @var array<Mesh>
     */
    private array $meshes;
    private int $numMeshes;
    private int $numVertices;
    private bool $hasRoom;

    /**
     * @var array<float>
     */
    private array $vertices;

    private int $vaoId = -1;
    private int $vboId = -1;
    private int $maxBatchSize;
    private Shader $shader;

    public function __construct(int $maxBatchSize)
    {
        $this->shader = new Shader(__DIR__ . '/../../resources/default.vertex.glsl', __DIR__ . '/../../resources/default.fragment.glsl');
        $this->shader->compile();

        $this->meshes = [];
        $this->maxBatchSize = $maxBatchSize;

        $this->vertices = [];

        $this->numMeshes = 0;
        $this->numVertices = 0;
        $this->hasRoom = true;
    }

    public function hasRoom(): bool
    {
        return $this->hasRoom;
    }

    public function render(Mat4 $projectionMatrix, Mat4 $viewMatrix): void
    {
        if (count($this->meshes) === 0) {
            return;
        }

        $this->shader->attach();
        $this->shader->uploadMat4('uProjection', $projectionMatrix);
        $this->shader->uploadMat4('uView', $viewMatrix);

        glBindVertexArray($this->vaoId);

        glEnableVertexAttribArray(0);
        glEnableVertexAttribArray(1);

        glDrawArrays(GL_TRIANGLES, 0, $this->numVertices);

        glDisableVertexAttribArray(0);
        glDisableVertexAttribArray(1);

        glBindVertexArray(0);

        $this->shader->detach();
    }

    public function addMesh(Mesh $mesh): void
    {
        $index = $this->numMeshes;

        $this->meshes[$index] = $mesh;
        $this->numMeshes++;

        $this->loadVertexProperties($index);
        $this->generateBuffers();

        if ($this->numMeshes >= $this->maxBatchSize) {
            $this->hasRoom = false;
        }
    }

    private function loadVertexProperties(int $index): void
    {
        $mesh = $this->meshes[$index];
        $gameObject = $mesh->getGameObject();
        $transform = $gameObject->getComponent(Transform::class);

        if ($transform === null) {
            return;
        }

        $vertices = $mesh->getVertices();

        $this->vertices = array_merge($this->vertices, $vertices);
        $this->numVertices += count($vertices) / 7;
    }

    private function generateBuffers(): void
    {
        glGenVertexArrays(1, $this->vaoId);
        glBindVertexArray($this->vaoId);

        glGenBuffers(1, $this->vboId);
        glBindBuffer(GL_ARRAY_BUFFER, $this->vboId);
        glBufferData(GL_ARRAY_BUFFER, new FloatBuffer($this->vertices), GL_STATIC_DRAW);

        glVertexAttribPointer(0, self::$posSize, GL_FLOAT, false, self::$vertexSizeBytes, self::$posOffset);
        glEnableVertexAttribArray(0);

        glVertexAttribPointer(1, self::$colorSize, GL_FLOAT, false, self::$vertexSizeBytes, self::$colorOffset);
        glEnableVertexAttribArray(1);
    }
}