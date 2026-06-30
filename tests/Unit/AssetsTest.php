<?php

namespace Mp\MLetter\Tests\Unit;

use Mp\MLetter\Support\Assets;
use PHPUnit\Framework\TestCase;

class AssetsTest extends TestCase
{
    public function test_it_builds_image_data_uri(): void
    {
        $this->assertStringStartsWith(
            'data:image/svg+xml;base64,',
            Assets::imageDataUri('logo-mojepanstwo-symbol.svg'),
        );
    }

    public function test_it_builds_font_data_uri(): void
    {
        $this->assertStringStartsWith(
            'data:font/woff;base64,',
            Assets::fontDataUri('SourceSansPro-Bold.woff'),
        );
    }
}
