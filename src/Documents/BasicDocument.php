<?php

namespace Mp\MLetter\Documents;

use Illuminate\Support\Str;
use Mp\MLetter\Contracts\PdfDocument;
use Mp\MLetter\Support\Typography;

class BasicDocument implements PdfDocument
{
    /** @var array<int, ?string> */
    private array $headingLines = [];

    private ?string $title = null;

    private string $bodyHtml = '';

    /** @var array<int, array{name: ?string, label: string}> */
    private array $signatures = [];

    public static function make(): self
    {
        return new self;
    }

    public function headingLine(?string $headingLine): self
    {
        $this->headingLines[] = $headingLine;

        return $this;
    }

    /**
     * @param array<int, ?string> $headingLines
     */
    public function headingLines(array $headingLines): self
    {
        $this->headingLines = $headingLines;

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
        return 'mletter::documents.basic-document';
    }

    /**
     * @return array<string, mixed>
     */
    public function data(): array
    {
        return [
            'headingLines' => $this->escapedHeadingLines(),
            'title' => $this->escapedLine($this->title),
            'bodyHtml' => Typography::nonBreakingPdfText($this->bodyHtml),
            'signatures' => $this->escapedSignatures(),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function escapedHeadingLines(): array
    {
        return collect($this->headingLines)
            ->map(fn (?string $line): ?string => $this->escapedLine($line))
            ->filter(fn (?string $line): bool => $line !== null)
            ->values()
            ->all();
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
