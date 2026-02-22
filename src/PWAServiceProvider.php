<?php

namespace Ladumor\LaravelPwa;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Ladumor\LaravelPwa\commands\PublishPWA;
use Ladumor\LaravelPwa\commands\DebugPWA;
use Ladumor\LaravelPwa\commands\GenerateManifest;

class PWAServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPwaUpdateNotifier();
        $this->registerLaravelPwa();
        $this->registerPwaInstallButton();
        $this->registerPwaDebug();
        $this->registerPwaAssets();
    }

    /**
     * Register pwaDebug blade directive
     *
     * @return void
     */
    protected function registerPwaDebug()
    {
        Blade::directive('pwaDebug', function () {
            if (config('app.debug')) {
                $version = time();
                $pwaDebug = asset('pwa-debug.js') . '?v=' . $version;
                return <<<HTML
<script src="{$pwaDebug}"></script>
HTML;
            }
            return '';
        });
    }

    /**
     * Register pwaUpdateNotifier blade directive
     *
     * @return void
     */
    protected function registerPwaUpdateNotifier()
    {
        Blade::directive('pwaUpdateNotifier', function () {
            return <<<'HTML'
<script>
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.ready.then(registration => {
        function notifyUpdate(worker) {
            console.log('[Laravel PWA] New version available');
            if (confirm('[Laravel PWA] New version available. Reload now?')) {
                worker.postMessage({ type: 'SKIP_WAITING' });
            }
        }

        if (registration.waiting) {
            notifyUpdate(registration.waiting);
        }

        registration.addEventListener('updatefound', () => {
            const newWorker = registration.installing;
            newWorker.addEventListener('statechange', () => {
                if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                    notifyUpdate(newWorker);
                }
            });
        });
    });

    navigator.serviceWorker.addEventListener('controllerchange', () => {
        window.location.reload();
    });
}
</script>
HTML;
        });
    }

    /**
     * Register laravelPwa blade directive
     *
     * @return void
     */
    protected function registerLaravelPwa()
    {
        Blade::directive('laravelPwa', function () {
            $version = time();
            $pwaInstall = asset('pwa-install.js') . '?v=' . $version;
            $bgSync = asset('background-sync.js') . '?v=' . $version;
            $swUrl = '/sw.js?v=' . $version;
            return <<<HTML
<script src="{$pwaInstall}"></script>
<script src="{$bgSync}"></script>
<script>
    if ("serviceWorker" in navigator) {
        // Register a service worker hosted at the root of the
        // site using the default scope.
        navigator.serviceWorker.register("{$swUrl}").then(
            (registration) => {
                console.log("Service worker registration succeeded:", registration);
            },
            (error) => {
                console.error(`Service worker registration failed: \${error}`);
            },
        );
    } else {
        console.error("Service workers are not supported.");
    }
</script>
HTML;
        });
    }

    /**
     * Register pwaInstallButton blade directive
     *
     * @return void
     */
    protected function registerPwaInstallButton()
    {
        Blade::directive('pwaInstallButton', function () {
            return <<<'HTML'
<button id="pwa-install-btn" style="display:none; position: fixed; bottom: 20px; right: 20px; padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 8px; z-index: 1000; cursor: pointer;">
   Install App
</button>
HTML;
        });
    }

    protected function registerPwaAssets()
    {
        Blade::directive('pwaAssets', function ($expressions) {

            $logo = empty($expressions['logo']) ? $expressions['logo'] : 'logo.png';
            $themeColor = empty($expressions['logo']) ? $expressions['logo'] : '#39B54A';

            return <<<'HTML'
<meta name="theme-color" content="{{$themeColor}}"/>
<link rel="apple-touch-icon" href="{{ asset($logo) }}">
<link rel="manifest" href="{{ asset('/manifest.json') }}">
HTML;
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
      $this->app->singleton('laravel-pwa:publish', function ($app) {
        return new PublishPWA();
       });

      $this->app->singleton('pwa:debug', function ($app) {
        return new DebugPWA();
       });

      $this->app->singleton('pwa:manifest', function ($app) {
        return new GenerateManifest();
       });

      $this->commands([
          'laravel-pwa:publish',
          'pwa:debug',
          'pwa:manifest',
      ]);
    }
}
