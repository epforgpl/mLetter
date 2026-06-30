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
        $this->addPageNumbers($dompdf);

        file_put_contents($path, $dompdf->output());
    }

    private function addPageNumbers(Dompdf $dompdf): void
    {
        if (! (bool) config('mletter.pdf.page_numbers.enabled', true)) {
            return;
        }

        $canvas = $dompdf->getCanvas();
        $pageCount = $canvas->get_page_count();

        if ($pageCount <= 1) {
            return;
        }

        $fontMetrics = $dompdf->getFontMetrics();
        $font = $fontMetrics->getFont(
            (string) config('mletter.pdf.page_numbers.font', 'Source Sans Pro'),
        ) ?: $fontMetrics->getFont('DejaVu Sans');
        $fontSize = (float) config('mletter.pdf.page_numbers.font_size', 8);
        $right = (float) config('mletter.pdf.page_numbers.right', 18);
        $bottom = (float) config('mletter.pdf.page_numbers.bottom', 10);
        $text = (string) config('mletter.pdf.page_numbers.text', '{PAGE_NUM} / {PAGE_COUNT}');
        $maxText = str_replace(['{PAGE_NUM}', '{PAGE_COUNT}'], [(string) $pageCount, (string) $pageCount], $text);
        $textWidth = $fontMetrics->getTextWidth($maxText, $font, $fontSize);
        $x = $canvas->get_width() - $this->mmToPoints($right) - $textWidth;
        $y = $canvas->get_height() - $this->mmToPoints($bottom);

        $canvas->page_text($x, $y, $text, $font, $fontSize, [0.35, 0.35, 0.35]);
    }

    private function mmToPoints(float $value): float
    {
        return $value * 72 / 25.4;
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
