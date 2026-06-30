<?php

namespace Mp\MLetter\Support;

class Assets
{
    /**
     * @var array<string, string>
     */
    private const MIME_TYPES = [
        'svg' => 'image/svg+xml',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
        'otf' => 'font/otf',
    ];

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

    public static function fontDataUri(string $file): string
    {
        return self::dataUri(self::font($file));
    }

    public static function imageDataUri(string $file): string
    {
        return self::dataUri(self::image($file));
    }

    public static function dataUri(string $path): string
    {
        if (! is_file($path) || ! is_readable($path)) {
            throw new \RuntimeException('mLetter asset is not readable: ' . $path);
        }

        return 'data:' . self::mimeType($path) . ';base64,' . base64_encode((string) file_get_contents($path));
    }

    public static function mimeType(string $path): string
    {
        $extension = strtolower((string) pathinfo($path, PATHINFO_EXTENSION));

        return self::MIME_TYPES[$extension] ?? 'application/octet-stream';
    }
}
