<?php

namespace Blaj\PhpEngine\Utils;

use GL\Math\Vec3;

class Vec3Utils
{

    public static function up(): Vec3
    {
        return new Vec3(0.0, 1.0, 0.0);
    }

    public static function down(): Vec3
    {
        return new Vec3(0.0, -1.0, 0.0);
    }

    public static function left(): Vec3
    {
        return new Vec3(-1.0, 0.0, 0.0);
    }

    public static function right(): Vec3
    {
        return new Vec3(1.0, 0.0, 0.0);
    }

    public static function forward(): Vec3
    {
        return new Vec3(0.0, 0.0, -1.0);
    }

    public static function back(): Vec3
    {
        return new Vec3(0.0, 0.0, 1.0);
    }

    public static function cross(Vec3 $vec1, Vec3 $vec2): Vec3
    {
        return new Vec3();
    }
}