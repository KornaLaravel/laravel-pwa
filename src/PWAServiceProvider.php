<?php

namespace Ladumor\LaravelPwa;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Ladumor\LaravelPwa\commands\PublishPWA;

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
    navigator.serviceWorker.addEventListener('message', event => {
        if (event.data?.type === 'NEW_VERSION_AVAILABLE') {
            console.log('[Laravel PWA] New version available');
            if (navigator.serviceWorker.controller) {
                navigator.serviceWorker.controller.postMessage({ type: 'SKIP_WAITING' });
            }
            window.location.reload();
        }
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
            return <<<'HTML'
<script src="{{ asset('pwa-install.js') }}"></script>
<script>
    if ("serviceWorker" in navigator) {
        // Register a service worker hosted at the root of the
        // site using the default scope.
        navigator.serviceWorker.register("/sw.js").then(
            (registration) => {
                console.log("Service worker registration succeeded:", registration);
            },
            (error) => {
                console.error(`Service worker registration failed: ${error}`);
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
<button id="pwa-install-btn" style="display:none; position: fixed; bottom: 20px; right: 20px; padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 8px; z-index: 1000;">
   Install App
</button>
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

      $this->commands([
          'laravel-pwa:publish',
      ]);
    }
}
