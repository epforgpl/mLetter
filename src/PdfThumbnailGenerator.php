<?php

namespace Mp\MLetter;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PdfThumbnailGenerator
{
    public function generate(string $disk, string $pdfPath, string $thumbnailPath): ?string
    {
        $pdfContent = Storage::disk($disk)->get($pdfPath);
        if (! $pdfContent) {
            return null;
        }

        $tmpDir = storage_path('tmp');
        if (! is_dir($tmpDir)) {
            mkdir($tmpDir, 0755, true);
        }

        $slug = Str::slug(pathinfo($pdfPath, PATHINFO_FILENAME) . '-' . uniqid());
        $localPdf = $tmpDir . '/' . $slug . '.pdf';
        $thumbBase = $tmpDir . '/' . $slug . '-thumb';
        $fullPath = $thumbBase . '-full.png';
        $thumbPath = $thumbBase . '.png';

        file_put_contents($localPdf, $pdfContent);

        try {
            if (shell_exec('which pdftoppm 2>/dev/null')) {
                passthru(sprintf(
                    'pdftoppm -png -f 1 -l 1 -r 150 %s %s',
                    escapeshellarg($localPdf),
                    escapeshellarg($thumbBase),
                ));

                foreach (glob($thumbBase . '*.png') as $file) {
                    rename($file, $fullPath);
                    break;
                }
            } elseif (shell_exec('which convert 2>/dev/null')) {
                passthru(sprintf(
                    'convert -density 150 %s[0] -alpha remove %s',
                    escapeshellarg($localPdf),
                    escapeshellarg($fullPath),
                ));
            }

            if (! file_exists($fullPath)) {
                return null;
            }

            if (shell_exec('which convert 2>/dev/null')) {
                passthru(sprintf(
                    'convert %s -gravity North -crop 100x80%%+0+0 +repage -resize 200x -quality 85 %s',
                    escapeshellarg($fullPath),
                    escapeshellarg($thumbPath),
                ));
            } elseif (shell_exec('which sips 2>/dev/null')) {
                copy($fullPath, $thumbPath);
                passthru(sprintf('sips --resampleWidth 300 %s >/dev/null 2>&1', escapeshellarg($thumbPath)));
            } else {
                copy($fullPath, $thumbPath);
            }

            if (! file_exists($thumbPath)) {
                return null;
            }

            Storage::disk($disk)->put($thumbnailPath, file_get_contents($thumbPath));

            return $thumbnailPath;
        } finally {
            foreach ([$localPdf, $fullPath, $thumbPath] as $path) {
                if (file_exists($path)) {
                    unlink($path);
                }
            }
        }
    }
}
