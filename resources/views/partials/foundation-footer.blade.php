<div class="mletter-footer">
    {{ $organizationName ?? config('mletter.brand.name', 'Fundacja Moje Państwo') }}@isset($organizationAddress) | {{ $organizationAddress }}@endisset<br>
    @isset($organizationIdentifiers){{ $organizationIdentifiers }}<br>@endisset
    @isset($organizationContact){{ $organizationContact }}@endisset
</div>
