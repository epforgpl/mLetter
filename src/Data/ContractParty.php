<?php

namespace Mp\MLetter\Data;

class ContractParty
{
    public function __construct(
        public readonly string $label,
        public readonly string $name,
        public readonly ?string $addressLine = null,
        public readonly ?string $postalCity = null,
        public readonly ?string $nip = null,
        public readonly ?string $pesel = null,
        public readonly ?string $representatives = null,
    ) {
    }
}
