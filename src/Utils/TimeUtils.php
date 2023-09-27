<?php

namespace Blaj\PhpEngine\Utils;

class TimeUtils
{
    private static ?float $timeStarted = null;

    public static function getTimeStarted(): float {
        if (self::$timeStarted === null) {
            self::$timeStarted = microtime(true);
        }

        return self::$timeStarted;
    }

    public static function getTime(): float {
        return (microtime(true) - self::getTimeStarted());
    }
}