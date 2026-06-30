@component('mletter::layouts.foundation-letter', [
    'showFooter' => true,
    'organizationName' => $organizationName,
    'organizationAddress' => $organizationAddress,
    'organizationIdentifiers' => $organizationIdentifiers,
    'organizationContact' => $organizationContact,
    'bodyFontSize' => '9.5pt',
    'bodyLineHeight' => '1.5',
    'styles' => '
        .date { text-align: right; color: #333; margin-top: 18mm; }
        .recipient { margin: 12mm 0 9mm auto; width: 48%; min-height: 20mm; }
        .subject { font-weight: 700; margin-bottom: 8mm; }
        .body p { margin: 0 0 4mm; }
        .body ul, .body ol { margin-top: 0; padding-left: 8mm; }
        .signature { margin-top: 16mm; }
    ',
])
    @if ($dateLine)
        <div class="date">{!! $dateLine !!}</div>
    @endif

    <div class="recipient">
        @if ($recipientName)
            <strong>{!! $recipientName !!}</strong><br>
        @endif
        @if ($recipientAddressHtml)
            {!! $recipientAddressHtml !!}<br>
        @endif
        @if ($recipientEmail)
            {!! $recipientEmail !!}
        @endif
    </div>

    @if ($subject)
        <div class="subject">Dotyczy: {!! $subject !!}</div>
    @endif

    <div class="body">{!! $bodyHtml !!}</div>

    @if ($signatureLines !== [])
        <div class="signature">
            @foreach ($signatureLines as $signatureLine)
                {!! $signatureLine !!}@if (! $loop->last)<br>@endif
            @endforeach
        </div>
    @endif
@endcomponent
