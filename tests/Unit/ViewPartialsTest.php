<?php

namespace Mp\MLetter\Tests\Unit;

use Mp\MLetter\MLetterServiceProvider;
use Orchestra\Testbench\TestCase;

class ViewPartialsTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [MLetterServiceProvider::class];
    }

    public function test_foundation_letter_layout_uses_dompdf_safe_builtin_fonts(): void
    {
        $html = view('mletter::layouts.foundation-letter', [
            'slot' => '<p>Treść</p>',
        ])->render();

        $this->assertStringContainsString('font-family: DejaVu Sans, sans-serif;', $html);
        $this->assertStringNotContainsString('@font-face', $html);
        $this->assertStringNotContainsString('file://', $html);
    }

    public function test_letterhead_embeds_logo_and_keeps_polish_characters(): void
    {
        $html = view('mletter::partials.foundation-letterhead')->render();

        $this->assertStringContainsString('data:image/svg+xml;base64,', $html);
        $this->assertStringContainsString('Fundacja Moje Państwo', $html);
        $this->assertStringNotContainsString('logo-mojepanstwo-symbol.svg', $html);
    }
}
