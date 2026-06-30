<?php

namespace Mp\MLetter;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelPdf\Facades\Pdf;

class GeneratedPdfRenderer
{
    public function __construct(private readonly PdfThumbnailGenerator $thumbnailGenerator)
    {
    }

    public function generate(Model $model): Model
    {
        $this->assertGeneratedPdfModel($model);

        if (method_exists($model, 'generatedPdfShouldGenerate') && ! $model->generatedPdfShouldGenerate()) {
            return $model;
        }

        [$top, $right, $bottom, $left] = $model->generatedPdfMargins();
        $path = $model->generatedPdfPath();
        $disk = $model->generatedPdfDisk();
        $document = $model->generatedPdfDocument();

        Pdf::view($document->view(), $document->data())
            ->driver((string) config('mletter.pdf.driver', 'dompdf'))
            ->format((string) config('mletter.pdf.format', 'a4'))
            ->margins($top, $right, $bottom, $left)
            ->disk($disk)
            ->save($path);

        $thumbnailPath = Storage::disk($disk)->exists($path)
            ? $this->thumbnailGenerator->generate(
                disk: $disk,
                pdfPath: $path,
                thumbnailPath: $model->generatedPdfThumbnailPath(),
            )
            : null;

        $model::withoutEvents(function () use ($model, $path, $thumbnailPath): void {
            $model->forceFill([
                'pdf_path' => $path,
                'pdf_generated_at' => now(),
                'thumbnail_path' => $thumbnailPath,
                'thumbnail_generated_at' => $thumbnailPath ? now() : null,
            ])->saveQuietly();
        });

        return $model->refresh();
    }

    private function assertGeneratedPdfModel(Model $model): void
    {
        foreach (['generatedPdfPath', 'generatedPdfDocument', 'generatedPdfMargins', 'generatedPdfDisk', 'generatedPdfThumbnailPath'] as $method) {
            if (! method_exists($model, $method)) {
                throw new \RuntimeException('Model does not implement generated PDF method: ' . $model::class . '::' . $method);
            }
        }
    }

}
