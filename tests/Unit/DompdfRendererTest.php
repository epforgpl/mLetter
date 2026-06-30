<?php

namespace Mp\MLetter\Tests\Unit;

use Mp\MLetter\DompdfRenderer;
use Mp\MLetter\MLetterServiceProvider;
use Orchestra\Testbench\TestCase;

class DompdfRendererTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [MLetterServiceProvider::class];
    }

    public function test_it_renders_with_writable_dompdf_font_cache(): void
    {
        $base = sys_get_temp_dir() . '/mletter-test-' . bin2hex(random_bytes(4));
        config([
            'mletter.pdf.dompdf.font_dir' => $base . '/fonts',
            'mletter.pdf.dompdf.font_cache' => $base . '/font-cache',
            'mletter.pdf.dompdf.temp_dir' => $base . '/tmp',
        ]);

        $path = $base . '/letter.pdf';

        app(DompdfRenderer::class)->render('mletter::layouts.foundation-letter', [
            'slot' => '<p>Zażółć gęślą jaźń</p>',
            'showFooter' => true,
        ], [18, 18, 18, 18], $path);

        $this->assertFileExists($path);
        $this->assertGreaterThan(0, filesize($path));
        $this->assertDirectoryExists($base . '/fonts');
        $this->assertDirectoryExists($base . '/font-cache');
        $this->assertDirectoryExists($base . '/tmp');
    }
}
