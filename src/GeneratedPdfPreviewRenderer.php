<?php

namespace Mp\MLetter;

use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelPdf\Facades\Pdf;

class GeneratedPdfPreviewRenderer
{
    public function __construct(private readonly DompdfRenderer $dompdfRenderer)
    {
    }

    public function render(Model $model, string $path): void
    {
        foreach (['generatedPdfDocument', 'generatedPdfMargins'] as $method) {
            if (! method_exists($model, $method)) {
                throw new \RuntimeException('Model does not implement generated PDF method: ' . $model::class . '::' . $method);
            }
        }

        [$top, $right, $bottom, $left] = $model->generatedPdfMargins();
        $document = $model->generatedPdfDocument();

        if ((string) config('mletter.pdf.driver', 'dompdf') === 'dompdf') {
            $this->dompdfRenderer->render($document->view(), $document->data(), [$top, $right, $bottom, $left], $path);

            return;
        }

        Pdf::view($document->view(), $document->data())
            ->driver((string) config('mletter.pdf.driver', 'dompdf'))
            ->format((string) config('mletter.pdf.format', 'a4'))
            ->margins($top, $right, $bottom, $left)
            ->save($path);
    }
}
