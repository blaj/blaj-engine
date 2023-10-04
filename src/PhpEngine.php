<?php

declare(strict_types=1);

namespace Blaj\PhpEngine;

use Blaj\PhpEngine\Container\Container;
use Blaj\PhpEngine\Core\SceneManager;
use Blaj\PhpEngine\Graphics\Window;
use Blaj\PhpEngine\Input\KeyboardListener;
use Blaj\PhpEngine\Input\MouseListener;
use Blaj\PhpEngine\Utils\TimeUtils;
use RuntimeException;

class PhpEngine
{

    public function run(): void
    {
        if (!glfwInit()) {
            throw new RuntimeException('GLFW could not be initialized!');
        }

        $container = new Container(false);

        $window =
            $container
                ->set(Window::class, fn(Container $container) => new Window(600, 400, 'Blaj engine'))
                ->get(Window::class);

        $sceneManager = $container->get(SceneManager::class);

        $mouseListener = $container->get(MouseListener::class);
        $keyboardListener = $container->get(KeyboardListener::class);

        glfwSetWindowSizeCallback($window->window, fn(int $width, int $height) => $window->windowSizeCallback($width, $height));
        glfwSetCursorPosCallback($window->window, fn(int $x, int $y) => $mouseListener->cursorPosCallback($x, $y));
        glfwSetMouseButtonCallback($window->window, fn(int $button, int $action, int $mods) => $mouseListener->mouseButtonCallback($button, $action, $mods));
        glfwSetScrollCallback($window->window, fn(float $xOffset, float $yOffset) => $mouseListener->mouseScrollCallback($xOffset, $yOffset));
        glfwSetKeyCallback($window->window, fn(int $key, int $scancode, int $action, int $mods) => $keyboardListener->keyCallback($key, $scancode, $action, $mods));

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
