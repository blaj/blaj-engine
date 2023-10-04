<?php

namespace Blaj\PhpEngine\Graphics;

use Blaj\PhpEngine\Core\Component;

class Mesh extends Component
{
    public function __construct(
        private array $vertices)
    {
    }

    public function getVertices(): array
    {
        return $this->vertices;
    }
}