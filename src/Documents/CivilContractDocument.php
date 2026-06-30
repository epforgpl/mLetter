<?php

namespace Mp\MLetter\Documents;

use Illuminate\Support\Str;
use Mp\MLetter\Contracts\PdfDocument;
use Mp\MLetter\Data\ContractParty;
use Mp\MLetter\Support\Typography;

class CivilContractDocument implements PdfDocument
{
    private ?string $heading = null;

    private ?string $number = null;

    private ?string $signedDate = null;

    private ?ContractParty $orderingParty = null;

    private ?ContractParty $contractorParty = null;

    private string $bodyHtml = '';

    private string $orderingSignatureLabel = 'Zamawiający';

    private string $contractorSignatureLabel = 'Wykonawca';

    public static function make(): self
    {
        return new self;
    }

    public function heading(?string $heading): self
    {
        $this->heading = $heading;

        return $this;
    }

    public function number(?string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function signedDate(?string $signedDate): self
    {
        $this->signedDate = $signedDate;

        return $this;
    }

    public function orderingParty(ContractParty $party): self
    {
        $this->orderingParty = $party;

        return $this;
    }

    public function contractorParty(ContractParty $party): self
    {
        $this->contractorParty = $party;

        return $this;
    }

    public function bodyMarkdown(?string $bodyMarkdown): self
    {
        $this->bodyHtml = (string) Str::of((string) $bodyMarkdown)->markdown([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        return $this;
    }

    public function signatureLabels(string $orderingSignatureLabel, string $contractorSignatureLabel): self
    {
        $this->orderingSignatureLabel = $orderingSignatureLabel;
        $this->contractorSignatureLabel = $contractorSignatureLabel;

        return $this;
    }

    public function view(): string
    {
        return 'mletter::documents.civil-contract';
    }

    /**
     * @return array<string, mixed>
     */
    public function data(): array
    {
        return [
            'heading' => $this->escapedLine($this->heading),
            'number' => $this->escapedLine($this->number),
            'signedDate' => $this->escapedLine($this->signedDate),
            'orderingParty' => $this->escapedParty($this->orderingParty),
            'contractorParty' => $this->escapedParty($this->contractorParty),
            'bodyHtml' => Typography::nonBreakingPdfText($this->bodyHtml),
            'orderingSignatureLabel' => (string) e($this->orderingSignatureLabel),
            'contractorSignatureLabel' => (string) e($this->contractorSignatureLabel),
        ];
    }

    private function escapedLine(?string $line): ?string
    {
        if ($line === null || $line === '') {
            return null;
        }

        return Typography::nonBreakingFoundationName((string) e($line));
    }

    /**
     * @return array{label: string, name: string, addressLine: ?string, postalCity: ?string, nip: ?string, pesel: ?string, representatives: ?string}
     */
    private function escapedParty(?ContractParty $party): array
    {
        if (! $party) {
            $party = new ContractParty(label: '', name: '');
        }

        return [
            'label' => (string) e($party->label),
            'name' => $this->escapedLine($party->name) ?? '',
            'addressLine' => $party->addressLine !== null ? (string) e($party->addressLine) : null,
            'postalCity' => $party->postalCity !== null ? (string) e($party->postalCity) : null,
            'nip' => $party->nip !== null ? (string) e($party->nip) : null,
            'pesel' => $party->pesel !== null ? (string) e($party->pesel) : null,
            'representatives' => $party->representatives !== null ? (string) e($party->representatives) : null,
        ];
    }
}
