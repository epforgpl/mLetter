<?php

namespace Mp\MLetter\Support;

class Assets
{
    public static function path(string $path): string
    {
        return dirname(__DIR__, 2) . '/resources/' . ltrim($path, '/');
    }

    public static function font(string $file): string
    {
        return self::path('fonts/' . $file);
    }

    public static function image(string $file): string
    {
        return self::path('images/' . $file);
    }
}
