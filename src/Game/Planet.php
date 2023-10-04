<?php

namespace Blaj\PhpEngine\Game;

use Blaj\PhpEngine\Core\GameObject;
use Blaj\PhpEngine\Utils\Vec3Utils;

class Planet extends GameObject
{

    public function initialize(): void
    {
        parent::initialize();

        foreach (self::directions() as $direction) {

        }
    }

    private static function directions(): array
    {
        return [
            Vec3Utils::up(),
            Vec3Utils::down(),
            Vec3Utils::left(),
            Vec3Utils::right(),
            Vec3Utils::forward(),
            Vec3Utils::back()
        ];
    }
}