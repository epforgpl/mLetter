<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <style>
        @include('mletter::partials.fonts')

        body {
            color: #111;
            font-family: 'Source Sans Pro', DejaVu Sans, sans-serif;
            font-size: 9.2pt;
            line-height: 1.38;
        }

        .mletter-logo {
            margin-bottom: 3mm;
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

        .organization-contact {
            color: #555;
            font-size: 8pt;
            line-height: 1.25;
            margin-bottom: 9mm;
            text-align: center;
        }

        .date-line {
            color: #555;
            margin-bottom: 8mm;
            text-align: right;
        }

        h1 {
            font-size: 13pt;
            font-weight: 700;
            margin: 0 0 2mm 0;
            text-align: center;
        }

        h2 {
            color: #555;
            font-size: 9.8pt;
            font-weight: 600;
            margin: 0 0 8mm 0;
            text-align: center;
        }

        .parties {
            margin-bottom: 8mm;
            width: 100%;
        }

        .parties td {
            padding: 2mm 0;
            vertical-align: top;
            width: 50%;
        }

        .label {
            color: #555;
            font-size: 8pt;
            font-weight: 700;
            letter-spacing: 0.04em;
            margin-bottom: 1mm;
            text-transform: uppercase;
        }

        .content {
            text-align: justify;
        }

        .content p {
            margin: 0 0 3mm 0;
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
            vertical-align: top;
            width: 50%;
        }

        .signatures .line {
            border-top: 1px solid #aaa;
            color: #555;
            display: inline-block;
            font-size: 8pt;
            padding-top: 2mm;
            width: 70%;
        }
    </style>
</head>
<body>
    @include('mletter::partials.letterhead')

    <div class="organization-contact">
        {{ config('mletter.brand.address', 'ul. Nowogrodzka 25/37, 00-511 Warszawa') }}<br>
        KRS: {{ config('mletter.brand.krs', '0000359730') }} | NIP: {{ config('mletter.brand.nip', '1231216692') }} | REGON: {{ config('mletter.brand.regon', '142445947') }}<br>
        {{ config('mletter.brand.email', 'biuro@mojepanstwo.pl') }} | {{ config('mletter.brand.website', 'https://mojepanstwo.pl/') }}
    </div>

    @if ($signedDate)
        <div class="date-line">{!! $signedDate !!}</div>
    @endif

    @if ($heading)
        <h1>{!! $heading !!}</h1>
    @endif
    @if ($number)
        <h2>nr {!! $number !!}</h2>
    @endif

    <table class="parties" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <p class="label">{!! $orderingParty['label'] !!}</p>
                @if ($orderingParty['name'])
                    <p><strong>{!! $orderingParty['name'] !!}</strong></p>
                @endif
                @if ($orderingParty['addressLine'])
                    <p>{!! $orderingParty['addressLine'] !!}</p>
                @endif
                @if ($orderingParty['postalCity'])
                    <p>{!! $orderingParty['postalCity'] !!}</p>
                @endif
                @if ($orderingParty['nip'])
                    <p>NIP: {!! $orderingParty['nip'] !!}</p>
                @endif
                @if ($orderingParty['representatives'])
                    <p style="margin-top: 0.5rem">Reprezentant: {!! $orderingParty['representatives'] !!}</p>
                @endif
            </td>
            <td>
                <div class="label">{!! $contractorParty['label'] !!}</div>
                @if ($contractorParty['name'])
                    <div><strong>{!! $contractorParty['name'] !!}</strong></div>
                @endif
                @if ($contractorParty['addressLine'])
                    <div>{!! $contractorParty['addressLine'] !!}</div>
                @endif
                @if ($contractorParty['postalCity'])
                    <div>{!! $contractorParty['postalCity'] !!}</div>
                @endif
                @if ($contractorParty['pesel'])
                    <div>PESEL: {!! $contractorParty['pesel'] !!}</div>
                @endif
                @if ($contractorParty['nip'])
                    <div>NIP: {!! $contractorParty['nip'] !!}</div>
                @endif
            </td>
        </tr>
    </table>

    <div class="content">
        {!! $bodyHtml !!}
    </div>

    <table class="signatures" cellspacing="0" cellpadding="0">
        <tr>
            <td><div class="line">{!! $orderingSignatureLabel !!}</div></td>
            <td><div class="line">{!! $contractorSignatureLabel !!}</div></td>
        </tr>
    </table>
</body>
</html>
