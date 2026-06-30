<?php

namespace Mp\MLetter;

use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\File;

class DompdfRenderer
{
    /**
     * @param array{0: int|float, 1: int|float, 2: int|float, 3: int|float} $margins
     */
    public function render(string $view, array $data, array $margins, string $path): void
    {
        $dompdf = new Dompdf($this->options());
        $dompdf->loadHtml($this->html($view, $data, $margins));
        $dompdf->setPaper((string) config('mletter.pdf.format', 'a4'));
        $dompdf->render();

        file_put_contents($path, $dompdf->output());
    }

    /**
     * @param array{0: int|float, 1: int|float, 2: int|float, 3: int|float} $margins
     */
    private function html(string $view, array $data, array $margins): string
    {
        [$top, $right, $bottom, $left] = $margins;

        $html = view($view, $data)->render();
        $css = "<style>@page { margin: {$top}mm {$right}mm {$bottom}mm {$left}mm; }</style>";

        if (preg_match('/<head([^>]*)>/i', $html)) {
            return (string) preg_replace('/<\/head>/i', $css . '</head>', $html, 1);
        }

        return $css . $html;
    }

    private function options(): Options
    {
        $options = new Options();
        $options->setIsRemoteEnabled((bool) config('mletter.pdf.dompdf.is_remote_enabled', false));
        $options->setDefaultFont((string) config('mletter.pdf.dompdf.default_font', 'Source Sans Pro'));
        $options->setFontDir($this->writablePath('font_dir', 'fonts'));
        $options->setFontCache($this->writablePath('font_cache', 'fonts'));
        $options->setTempDir($this->writablePath('temp_dir', 'tmp'));

        $chroot = config('mletter.pdf.dompdf.chroot');
        if ($chroot !== null && $chroot !== '') {
            $options->setChroot($chroot);
        }

        return $options;
    }

    private function writablePath(string $key, string $fallback): string
    {
        $path = config("mletter.pdf.dompdf.{$key}");

        if (! is_string($path) || trim($path) === '') {
            $path = function_exists('storage_path')
                ? storage_path('app/mletter/dompdf/' . $fallback)
                : sys_get_temp_dir() . '/mletter/dompdf/' . $fallback;
        }

        File::ensureDirectoryExists($path);

        return $path;
    }
}
