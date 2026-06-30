<?php

namespace Mp\MLetter\Contracts;

interface PdfDocument
{
    public function view(): string;

    /**
     * @return array<string, mixed>
     */
    public function data(): array;
}
