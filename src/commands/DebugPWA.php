<?php

namespace Ladumor\LaravelPwa\commands;

use Illuminate\Console\Command;
use Ladumor\LaravelPwa\FileGenerator;

class DebugPWA extends Command
{
    use FileGenerator;
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'pwa:debug';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enable or show PWA debug information.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $publicDir = public_path();
        $debugTemplate = file_get_contents(__DIR__ . '/../stubs/pwa-debug.stub');
        $this->createFile($publicDir . DIRECTORY_SEPARATOR, 'pwa-debug.js', $debugTemplate);

        $this->info('pwa-debug.js (Debug Tool) file is published.');

        $this->info('PWA Debug Tool');
        $this->line('----------------');
        $this->line('This command helps you debug your PWA implementation.');
        $this->line('');
        $this->info('Available Tips:');
        $this->bulletList([
            'Check Browser Console: Detailed logs are sent to the browser console when debugging is active.',
            'HTTPS Requirement: PWAs only work over HTTPS or localhost.',
            'Service Worker State: Check Chrome DevTools > Application > Service Workers.',
            'Cache Storage: Check Chrome DevTools > Application > Cache Storage.',
        ]);

        $this->line('');
        $this->info('Usage:');
        $this->line('Add @pwaDebug to your blade file to enable the on-screen debug helper.');
    }

    /**
     * Provide bullet list for console
     *
     * @param array $items
     */
    protected function bulletList(array $items)
    {
        foreach ($items as $item) {
            $this->line(' • ' . $item);
        }
    }
}
