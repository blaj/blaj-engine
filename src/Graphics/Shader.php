<?php

declare(strict_types=1);

namespace Blaj\PhpEngine\Graphics;

use GL\Buffer\FloatBuffer;
use GL\Math\Mat4;
use RuntimeException;

class Shader
{

    private readonly int $shaderProgramId;
    private readonly string $fragmentSource;
    private readonly string $vertexSource;

    public function __construct(private readonly string $vertexPath, private readonly string $fragmentPath)
    {
        $vertexSource = file_get_contents($this->vertexPath);
        $this->vertexSource = !is_bool($vertexSource) ? $vertexSource : throw new RuntimeException('');

        $fragmentSource = file_get_contents($this->fragmentPath);
        $this->fragmentSource = !is_bool($fragmentSource) ? $fragmentSource : throw new RuntimeException('');
    }

    public function compile(): void
    {
        $this->shaderProgramId = glCreateProgram();

        $success = -1;

        $vertexId = glCreateShader(GL_VERTEX_SHADER);
        glShaderSource($vertexId, $this->vertexSource);
        glCompileShader($vertexId);
        glGetShaderiv($vertexId, GL_COMPILE_STATUS, $success);
        if (!$success) {
            throw new RuntimeException('Vertex shader could not be compiled.');
        }

        $fragmentId = glCreateShader(GL_FRAGMENT_SHADER);
        glShaderSource($fragmentId, $this->fragmentSource);
        glCompileShader($fragmentId);
        glGetShaderiv($fragmentId, GL_COMPILE_STATUS, $success);
        if (!$success) {
            throw new RuntimeException('Fragment shader could not be compiled.');
        }

        glAttachShader($this->shaderProgramId, $vertexId);
        glAttachShader($this->shaderProgramId, $fragmentId);
        glLinkProgram($this->shaderProgramId);
        glGetProgramiv($this->shaderProgramId, GL_LINK_STATUS, $success);
        if (!$success) {
            throw new RuntimeException('Shader could not be linked');
        }

        glDeleteShader($vertexId);
        glDeleteShader($fragmentId);
    }

    public function attach(): void
    {
        glUseProgram($this->shaderProgramId);

    }

    public function detach(): void
    {
        glUseProgram(0);
    }

    public function uploadMat4(string $varName, Mat4 $mat4): void {
        $varLocation = glGetUniformLocation($this->shaderProgramId, $varName);
        $floatBuffer = new FloatBuffer();
        $floatBuffer->pushMat4($mat4);
        glUniformMatrix4fv($varLocation, false, $floatBuffer);
    }
}