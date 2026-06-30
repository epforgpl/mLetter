<?php

namespace Mp\MLetter\Support;

class Typography
{
    public static function nonBreakingFoundationName(string $value): string
    {
        return str_replace(
            ['Fundacji Moje Państwo', 'Fundacja Moje Państwo'],
            ['Fundacji&nbsp;Moje&nbsp;Państwo', 'Fundacja&nbsp;Moje&nbsp;Państwo'],
            $value,
        );
    }

    public static function nonBreakingAmounts(string $value): string
    {
        return preg_replace_callback('/\d+(?:[ \x{00A0}]\d{3})*(?:,\d{2})?[ \x{00A0}]+zł/u', function (array $amount): string {
            return preg_replace('/[ \x{00A0}]+/u', '&nbsp;', $amount[0]) ?? $amount[0];
        }, $value) ?? $value;
    }

    public static function nonBreakingPdfText(string $html): string
    {
        return preg_replace_callback('/(<[^>]+>)|([^<]+)/u', function (array $matches): string {
            if (($matches[1] ?? '') !== '') {
                return $matches[1];
            }

            return self::nonBreakingAmounts(self::nonBreakingFoundationName($matches[2] ?? ''));
        }, $html) ?? $html;
    }
}
