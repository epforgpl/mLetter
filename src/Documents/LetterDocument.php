<?php

namespace Mp\MLetter\Documents;

use Mp\MLetter\Contracts\PdfDocument;
use Mp\MLetter\Support\Typography;

class LetterDocument implements PdfDocument
{
    private ?string $dateLine = null;

    private ?string $recipientName = null;

    private ?string $recipientAddress = null;

    private ?string $recipientEmail = null;

    private ?string $subject = null;

    private string $bodyHtml = '';

    /** @var array<int, string> */
    private array $signatureLines = [];

    private ?string $organizationName = null;

    private ?string $organizationAddress = null;

    private ?string $organizationIdentifiers = null;

    private ?string $organizationContact = null;

    public static function make(): self
    {
        return new self;
    }

    public function dateLine(?string $dateLine): self
    {
        $this->dateLine = $dateLine;

        return $this;
    }

    public function recipient(?string $name, ?string $address = null, ?string $email = null): self
    {
        $this->recipientName = $name;
        $this->recipientAddress = $address;
        $this->recipientEmail = $email;

        return $this;
    }

    public function subject(?string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function bodyHtml(?string $bodyHtml): self
    {
        $this->bodyHtml = (string) $bodyHtml;

        return $this;
    }

    /**
     * @param array<int, string> $signatureLines
     */
    public function signatureLines(array $signatureLines): self
    {
        $this->signatureLines = $signatureLines;

        return $this;
    }

    public function footer(?string $name = null, ?string $address = null, ?string $identifiers = null, ?string $contact = null): self
    {
        $this->organizationName = $name;
        $this->organizationAddress = $address;
        $this->organizationIdentifiers = $identifiers;
        $this->organizationContact = $contact;

        return $this;
    }

    public function view(): string
    {
        return 'mletter::documents.letter';
    }

    /**
     * @return array<string, mixed>
     */
    public function data(): array
    {
        return [
            'dateLine' => $this->escapedLine($this->dateLine),
            'recipientName' => $this->escapedLine($this->recipientName),
            'recipientAddressHtml' => $this->escapedMultiline($this->recipientAddress),
            'recipientEmail' => $this->escapedLine($this->recipientEmail),
            'subject' => $this->escapedLine($this->subject),
            'bodyHtml' => Typography::nonBreakingPdfText($this->bodyHtml),
            'signatureLines' => collect($this->signatureLines)
                ->map(fn (string $line): string => $this->escapedLine($line) ?? '')
                ->filter(fn (string $line): bool => $line !== '')
                ->values()
                ->all(),
            'organizationName' => $this->organizationName,
            'organizationAddress' => $this->organizationAddress,
            'organizationIdentifiers' => $this->organizationIdentifiers,
            'organizationContact' => $this->organizationContact,
        ];
    }

    private function escapedLine(?string $line): ?string
    {
        $line = trim((string) $line);

        if ($line === '') {
            return null;
        }

        return Typography::nonBreakingFoundationName((string) e($line));
    }

    private function escapedMultiline(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        return nl2br((string) e($value), false);
    }
}
