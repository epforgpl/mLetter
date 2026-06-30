<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <style>
        @include('mletter::partials.fonts')

        @page { margin: {{ $pageMargin ?? '18mm 18mm 28mm 18mm' }}; }

        body {
            color: {{ $bodyColor ?? '#1f2937' }};
            font-family: 'Source Sans Pro', DejaVu Sans, sans-serif;
            font-size: {{ $bodyFontSize ?? '9pt' }};
            line-height: {{ $bodyLineHeight ?? '1.3' }};
            padding-bottom: {{ $bodyPaddingBottom ?? '10mm' }};
        }

        .mletter-logo {
            text-align: center;
        }

        .mletter-logo img {
            display: block;
            margin: 0 auto;
            width: {{ $logoWidth ?? '12mm' }};
        }

        .mletter-logo-title {
            color: {{ config('mletter.brand.primary_color', '#364F87') }};
            font-family: sofia-pro, 'Source Sans Pro', DejaVu Sans, sans-serif;
            font-size: {{ $logoTitleFontSize ?? '10pt' }};
            font-weight: 700;
            margin-top: {{ $logoTitleMarginTop ?? '1mm' }};
            text-align: center;
        }

        .mletter-footer {
            border-top: 1px solid {{ config('mletter.brand.rule_color', '#AEB8D6') }};
            bottom: {{ $footerBottom ?? '-10mm' }};
            color: {{ config('mletter.brand.primary_color', '#364F87') }};
            font-size: {{ $footerFontSize ?? '7.2pt' }};
            left: 0;
            line-height: 1.2;
            padding-top: 2mm;
            position: fixed;
            right: 0;
            text-align: center;
        }

        {{ $styles ?? '' }}
    </style>
</head>
<body>
    @include('mletter::partials.letterhead')

    {{ $slot ?? '' }}

    @isset($showFooter)
        @if ($showFooter)
            @include('mletter::partials.footer')
        @endif
    @endisset
</body>
</html>
