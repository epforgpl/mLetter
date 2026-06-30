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

    public function test_fonts_are_embedded_as_data_uris(): void
    {
        $html = view('mletter::partials.fonts')->render();

        $this->assertStringContainsString('data:font/woff;base64,', $html);
        $this->assertStringContainsString('data:font/ttf;base64,', $html);
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
