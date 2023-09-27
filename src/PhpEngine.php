<?php

declare(strict_types=1);

namespace Blaj\PhpEngine;

use Blaj\PhpEngine\Core\SceneManager;
use Blaj\PhpEngine\Graphics\Window;
use Blaj\PhpEngine\Utils\TimeUtils;
use RuntimeException;

class PhpEngine
{

    public function run(): void
    {
        if (!glfwInit()) {
            throw new RuntimeException('GLFW could not be initialized!');
        }

        $window = new Window(600, 400, 'Blaj engine');
        $window->init();

        $sceneManager = new SceneManager($window->width, $window->height);

        $beginTime = TimeUtils::getTime();
        $deltaTime = -1.0;

        while (!glfwWindowShouldClose($window->window)) {
            $window->render(function () use ($sceneManager) {
                $sceneManager->render();
            });

            if ($deltaTime >= 0) {
                $sceneManager->update($deltaTime);

                echo sprintf('FPS: %d %s', (1.0 / $deltaTime), PHP_EOL);
            }

            $endTime = TimeUtils::getTime();
            $deltaTime = $endTime - $beginTime;
            $beginTime = $endTime;
        }

        glfwDestroyWindow($window->window);
        glfwTerminate();
    }
}
