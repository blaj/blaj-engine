<?php

namespace Blaj\PhpEngine\Graphics;

use Blaj\PhpEngine\Core\Component;
use GL\Buffer\FloatBuffer;
use GL\Buffer\IntBuffer;
use GL\Math\Mat4;

class Mesh extends Component
{

    private int $vaoId = -1;
    private int $vboId = -1;

    private Shader $shader;

    public function __construct(array $vertices, private Mat4 $projectionMatrix, private Mat4 $viewMatrix)
    {
        $this->shader = new Shader(__DIR__ . '/../../resources/default.vertex.glsl', __DIR__ . '/../../resources/default.fragment.glsl');
        $this->shader->compile();

        glGenVertexArrays(1, $this->vaoId);
        glBindVertexArray($this->vaoId);

        glGenBuffers(1, $this->vboId);
        glBindBuffer(GL_ARRAY_BUFFER, $this->vboId);
        glBufferData(GL_ARRAY_BUFFER, new FloatBuffer($vertices), GL_STATIC_DRAW);

        glVertexAttribPointer(0, 3, GL_FLOAT, false, GL_SIZEOF_FLOAT * 6, 0);
        glEnableVertexAttribArray(0);

        glVertexAttribPointer(1, 3, GL_FLOAT, false, GL_SIZEOF_FLOAT * 6, GL_SIZEOF_FLOAT * 3);
        glEnableVertexAttribArray(1);

        glBindBuffer(GL_ARRAY_BUFFER, 0);
        glBindVertexArray(0);
    }

    function render(): void
    {
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

    function update(float $deltaTime): void
    {
    }
}