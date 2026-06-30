<div class="mletter-footer">
    {{ $organizationName ?? config('mletter.brand.name', 'Fundacja Moje Państwo') }} | {{ $organizationAddress ?? config('mletter.brand.address', 'ul. Nowogrodzka 25/37, 00-511 Warszawa') }}<br>
    {{ $organizationIdentifiers ?? 'KRS: ' . config('mletter.brand.krs', '0000359730') . ' | NIP: ' . config('mletter.brand.nip', '1231216692') . ' | REGON: ' . config('mletter.brand.regon', '142445947') }}<br>
    {{ $organizationContact ?? config('mletter.brand.email', 'biuro@mojepanstwo.pl') . ' | ' . config('mletter.brand.website', 'https://mojepanstwo.pl/') }}
</div>
