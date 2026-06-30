<?php

namespace Mp\MLetter;

use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelPdf\Facades\Pdf;

class GeneratedPdfPreviewRenderer
{
    public function render(Model $model, string $path): void
    {
        foreach (['generatedPdfView', 'generatedPdfViewData', 'generatedPdfMargins'] as $method) {
            if (! method_exists($model, $method)) {
                throw new \RuntimeException('Model does not implement generated PDF method: ' . $model::class . '::' . $method);
            }
        }

        [$top, $right, $bottom, $left] = $model->generatedPdfMargins();

        Pdf::view($model->generatedPdfView(), $model->generatedPdfViewData())
            ->driver((string) config('mletter.pdf.driver', 'dompdf'))
            ->format((string) config('mletter.pdf.format', 'a4'))
            ->margins($top, $right, $bottom, $left)
            ->save($path);
    }
}
