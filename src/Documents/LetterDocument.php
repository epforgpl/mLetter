<?php

namespace Mp\MLetter\Documents;

use Illuminate\Support\Str;
use Mp\MLetter\Contracts\PdfDocument;
use Mp\MLetter\Support\Typography;

class LetterDocument implements PdfDocument
{
    private ?string $typeLine = null;

    private ?string $organizationLine = null;

    private ?string $dateLine = null;

    private ?string $title = null;

    private string $bodyHtml = '';

    /** @var array<int, array{name: ?string, label: string}> */
    private array $signatures = [];

    public static function make(): self
    {
        return new self;
    }

    public function typeLine(?string $typeLine): self
    {
        $this->typeLine = $typeLine;

        return $this;
    }

    public function organizationLine(?string $organizationLine): self
    {
        $this->organizationLine = $organizationLine;

        return $this;
    }

    public function dateLine(?string $dateLine): self
    {
        $this->dateLine = $dateLine;

        return $this;
    }

    public function title(?string $title): self
    {
        $this->title = $title;

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

    /**
     * @param array<int, array{name: ?string, label: string}> $signatures
     */
    public function signatures(array $signatures): self
    {
        $this->signatures = $signatures;

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
            'typeLine' => $this->escapedLine($this->typeLine),
            'organizationLine' => $this->escapedLine($this->organizationLine),
            'dateLine' => $this->escapedLine($this->dateLine),
            'title' => $this->escapedLine($this->title),
            'bodyHtml' => Typography::nonBreakingPdfText($this->bodyHtml),
            'signatures' => $this->escapedSignatures(),
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
     * @return array<int, array{name: ?string, label: string}>
     */
    private function escapedSignatures(): array
    {
        return collect($this->signatures)
            ->map(fn (array $signature): array => [
                'name' => filled($signature['name'] ?? null) ? (string) e($signature['name']) : null,
                'label' => (string) e($signature['label'] ?? ''),
            ])
            ->all();
    }
}
