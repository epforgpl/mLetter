<?php

namespace Mp\MLetter\Tests\Unit;

use Mp\MLetter\Support\Typography;
use PHPUnit\Framework\TestCase;

class TypographyTest extends TestCase
{
    public function test_it_keeps_foundation_name_together(): void
    {
        $this->assertSame(
            'Fundacji&nbsp;Moje&nbsp;Państwo',
            Typography::nonBreakingFoundationName('Fundacji Moje Państwo'),
        );
    }

    public function test_it_keeps_amounts_together(): void
    {
        $this->assertSame(
            'Kwota 3&nbsp;333&nbsp;744,13&nbsp;zł',
            Typography::nonBreakingAmounts('Kwota 3 333 744,13 zł'),
        );
    }

    public function test_it_does_not_change_html_tags(): void
    {
        $this->assertSame(
            '<p data-value="3 333 zł">Kwota 3&nbsp;333&nbsp;zł</p>',
            Typography::nonBreakingPdfText('<p data-value="3 333 zł">Kwota 3 333 zł</p>'),
        );
    }
}
