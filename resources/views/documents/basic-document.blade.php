<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <style>
        @include('mletter::partials.fonts')

        body {
            color: #1f2937;
            font-family: 'Source Sans Pro', DejaVu Sans, sans-serif;
            font-size: 9pt;
            line-height: 1.3;
            padding-bottom: 10mm;
        }

        .mletter-logo {
            text-align: center;
        }

        .mletter-logo img {
            display: block;
            margin: 0 auto;
            width: 12mm;
        }

        .mletter-logo-title {
            color: #364F87;
            font-family: sofia-pro, 'Source Sans Pro', DejaVu Sans, sans-serif;
            font-size: 10pt;
            font-weight: 700;
            margin-top: 1mm;
            text-align: center;
        }

        .document-heading {
            font-size: 9.5pt;
            font-weight: 400;
            margin-top: 5mm;
            margin-bottom: 10mm;
            text-align: center;
        }

        .document-title {
            font-size: 10pt;
            font-weight: 700;
            margin: 5mm auto 17mm;
            max-width: 140mm;
        }

        .content h1,
        .content h2,
        .content h3 {
            font-size: 10pt;
            margin: 7mm 0 3mm 0;
            text-align: center;
        }

        .content p {
            margin: 0 0 3mm 0;
            text-align: justify;
        }

        .content ol,
        .content ul {
            margin: 0 0 3mm 7mm;
            padding: 0;
        }

        .signatures {
            margin-top: 18mm;
            width: 100%;
        }

        .signatures td {
            padding-top: 12mm;
            text-align: center;
            width: 50%;
        }

        .signature-name {
            font-weight: 700;
        }

        .mletter-footer {
            border-top: 1px solid #AEB8D6;
            bottom: -10mm;
            color: #364F87;
            font-size: 7.2pt;
            left: 0;
            line-height: 1.2;
            padding-top: 2mm;
            position: fixed;
            right: 0;
            text-align: center;
        }
    </style>
</head>
<body>
    @include('mletter::partials.letterhead')

    <div class="document-heading">
        @foreach ($headingLines as $headingLine)
            <div class="document-heading-line">{!! $headingLine !!}</div>
        @endforeach
        @if ($title)
            <div class="document-title">{!! $title !!}</div>
        @endif
    </div>

    <div class="content">
        {!! $bodyHtml !!}
    </div>

    @if ($signatures !== [])
        <table class="signatures">
            @foreach (array_chunk($signatures, 2) as $signatureRow)
                <tr>
                    @foreach ($signatureRow as $signature)
                        <td @if (count($signatureRow) === 1) colspan="2" @endif>
                            ...............................................<br>
                            @if ($signature['name'])
                                <span class="signature-name">{!! $signature['name'] !!}</span><br>
                            @endif
                            {!! $signature['label'] !!}
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </table>
    @endif

    @include('mletter::partials.footer')
</body>
</html>
