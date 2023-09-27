<?php

namespace Blaj\PhpEngine\Graphics;

use GL\Buffer\FloatBuffer;

class BatchRenderer
{

    private static int $posSize = 3;
    private static int $colorSize = 4;

    private static int $posOffset = 0;
    private static int $colorOffset = 0 + 3 * GL_SIZEOF_FLOAT;

    private static int $vertexSize = 6;
    private static int $vertexSizeBytes = 6 * GL_SIZEOF_FLOAT;

    /**
     * @var array<Mesh>
     */
    private array $meshes;
    private int $numMeshes;
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
        $this->shader = new Shader('', '');
        $this->shader->compile();

        $this->meshes = [];
        $this->maxBatchSize = $maxBatchSize;

        $this->vertices = [];

        $this->numMeshes = 0;
        $this->hasRoom = true;
    }

    public function hasRoom(): bool {
        return $this->hasRoom;
    }

    public function initialize(): void {
        glGenVertexArrays(1, $this->vaoId);
        glBindVertexArray($this->vaoId);

        glGenBuffers(1, $this->vboId);
        glBindBuffer(GL_ARRAY_BUFFER, $this->vboId);
        glBufferData(GL_ARRAY_BUFFER, new FloatBuffer($this->vertices), GL_DYNAMIC_DRAW);

        glVertexAttribPointer(0, self::$posSize, GL_FLOAT, false, self::$vertexSizeBytes, self::$posOffset);
        glEnableVertexAttribArray(0);

        glVertexAttribPointer(1, self::$colorSize, GL_FLOAT, false, self::$vertexSizeBytes, self::$colorOffset);
        glEnableVertexAttribArray(1);
    }

    public function render(): void {
        $this->shader->attach();
        $this->shader->uploadMat4('uProjection', $this->projectionMatrix);
        $this->shader->uploadMat4('uView', $this->viewMatrix);

        glBindVertexArray($this->vaoId);

        glEnableVertexAttribArray(0);
        glEnableVertexAttribArray(1);

        glDrawArrays(GL_TRIANGLES, 0, 3);

        glDisableVertexAttribArray(0);
        glDisableVertexAttribArray(1);

        glBindVertexArray(0);

        $this->shader->detach();
    }

    public function addMesh(Mesh $mesh): void {
        $index = $this->numMeshes;

        $this->meshes[$index] = $mesh;
        $this->numMeshes++;

        $this->loadVertexProperties($index);

        if ($this->numMeshes >= $this->maxBatchSize) {
            $this->hasRoom = false;
        }
    }

    private function loadVertexProperties(int $index): void
    {
        $mesh = $this->meshes[$index];
        $offset = $index = 4 * self::$vertexSize;

        $xAdd = 1.0;
        $yAdd = 1.0;

        for ($i = 0; $i < 4; $i++) {
            if ($i === 1) {
                $yAdd = 0.0;
            } else if ($i === 2) {
                $xAdd = 0.0;
            } else if ($i === 3) {
                $yAdd = 1.0;
            }

//            $this->vertices[$offset]
        }
    }
}