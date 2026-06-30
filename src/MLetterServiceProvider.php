<?php

namespace Mp\MLetter;

use Illuminate\Support\ServiceProvider;
use Mp\MLetter\Support\Assets;

class MLetterServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/mletter.php', 'mletter');

        $this->app->singleton(Assets::class);
        $this->app->singleton(DompdfRenderer::class);
        $this->app->singleton(GeneratedPdfRenderer::class);
        $this->app->singleton(GeneratedPdfPreviewRenderer::class);
        $this->app->singleton(PdfThumbnailGenerator::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'mletter');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/mletter.php' => config_path('mletter.php'),
            ], 'mletter-config');

            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/mletter'),
            ], 'mletter-views');

            $this->publishes([
                __DIR__ . '/../resources/fonts' => public_path(config('mletter.assets.publish_path', 'vendor/mletter') . '/fonts'),
                __DIR__ . '/../resources/images' => public_path(config('mletter.assets.publish_path', 'vendor/mletter') . '/images'),
            ], 'mletter-assets');
        }
    }
}
