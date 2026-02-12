<?php

namespace Ladumor\LaravelPwa\commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateManifest extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'pwa:manifest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Interactively generate manifest.json for PWA application.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->info('Welcome to the PWA Manifest Generator!');

        $name = $this->ask('What is the name of your application?', 'Laravel PWA');
        $shortName = $this->ask('What is the short name of your application?', 'PWA');
        $description = $this->ask('Provide a brief description of your application', 'My Awesome Laravel PWA');
        $startUrl = $this->ask('What is the start URL?', '/index.php');
        $themeColor = $this->ask('What is the theme color (hex)?', '#6777ef');
        $backgroundColor = $this->ask('What is the background color (hex)?', '#ffffff');
        $display = $this->choice('Select display mode', ['fullscreen', 'standalone', 'minimal-ui', 'browser'], 1);
        $orientation = $this->choice('Select orientation', ['any', 'natural', 'portrait', 'landscape'], 0);

        $manifest = [
            'name' => $name,
            'short_name' => $shortName,
            'description' => $description,
            'start_url' => $startUrl,
            'background_color' => $backgroundColor,
            'theme_color' => $themeColor,
            'display' => $display,
            'orientation' => $orientation,
            'icons' => $this->getIconConfig()
        ];

        $manifestJson = json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        
        // We use the same VERSION placeholder logic as in PublishPWA
        $manifestJson = str_replace('"icon-72x72.png"', '"icon-72x72.png?v={{VERSION}}"', $manifestJson);
        $manifestJson = str_replace('"icon-96x96.png"', '"icon-96x96.png?v={{VERSION}}"', $manifestJson);
        $manifestJson = str_replace('"icon-128x128.png"', '"icon-128x128.png?v={{VERSION}}"', $manifestJson);
        $manifestJson = str_replace('"icon-144x144.png"', '"icon-144x144.png?v={{VERSION}}"', $manifestJson);
        $manifestJson = str_replace('"icon-152x152.png"', '"icon-152x152.png?v={{VERSION}}"', $manifestJson);
        $manifestJson = str_replace('"icon-192x192.png"', '"icon-192x192.png?v={{VERSION}}"', $manifestJson);
        $manifestJson = str_replace('"icon-384x384.png"', '"icon-384x384.png?v={{VERSION}}"', $manifestJson);
        $manifestJson = str_replace('"icon-512x512.png"', '"icon-512x512.png?v={{VERSION}}"', $manifestJson);

        $stubPath = __DIR__ . '/../stubs/manifest.stub';
        
        if (File::put($stubPath, $manifestJson)) {
            $this->info('Success! The manifest.stub has been updated with your preferences.');
            $this->comment('Note: Run "php artisan laravel-pwa:publish" to apply these changes to your public/manifest.json');
        } else {
            $this->error('Failed to write to manifest.stub');
        }
    }

    /**
     * Get default icon configuration
     *
     * @return array
     */
    protected function getIconConfig()
    {
        $sizes = ['72x72', '96x96', '128x128', '144x144', '152x152', '192x192', '384x384', '512x512'];
        $icons = [];

        foreach ($sizes as $size) {
            $icons[] = [
                'src' => "icon-{$size}.png",
                'sizes' => $size,
                'type' => 'image/png',
                'purpose' => 'any'
            ];
        }

        return $icons;
    }
}
