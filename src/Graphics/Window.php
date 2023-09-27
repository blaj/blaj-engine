<?php

declare(strict_types = 1);

namespace Blaj\PhpEngine\Graphics;

use RuntimeException;

class Window {

    public readonly \GLFWWindow $window;

    public function __construct(
        public readonly int $width, 
        public readonly int $height, 
        public readonly string $title) {

    }

    public function init(): void {
        glfwWindowHint(GLFW_RESIZABLE, GL_TRUE);

        glfwWindowHint(GLFW_CONTEXT_VERSION_MAJOR, 4);
        glfwWindowHint(GLFW_CONTEXT_VERSION_MINOR, 1);
        glfwWindowHint(GLFW_OPENGL_PROFILE, GLFW_OPENGL_CORE_PROFILE);
        
        glfwWindowHint(GLFW_OPENGL_FORWARD_COMPAT, GL_TRUE);
        
        if (!$this->window = glfwCreateWindow($this->width, $this->height, $this->title)) {
            throw new RuntimeException('OS Window could not be initialized!');
        }

        glfwMakeContextCurrent($this->window);
        glfwSwapInterval(1);
    }

    public function render(\Closure $closure): void {
        glClearColor(0, 0, 0, 1);
        glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);
    
        $closure();

        glfwSwapBuffers($this->window);
        glfwPollEvents();
    }
}
