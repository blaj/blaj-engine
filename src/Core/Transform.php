<?php

namespace Blaj\PhpEngine\Core;

use GL\Math\Vec3;

class Transform extends Component
{
    public function __construct(
        private Vec3 $position = new Vec3(),
        private Vec3 $scale = new Vec3())
    {
    }

    public function getPosition(): Vec3
    {
        return $this->position;
    }

    public function setPosition(Vec3 $position): self
    {
        $this->position = $position;
        return $this;
    }

    public function getScale(): Vec3
    {
        return $this->scale;
    }

    public function setScale(Vec3 $scale): self
    {
        $this->scale = $scale;
        return $this;
    }
}