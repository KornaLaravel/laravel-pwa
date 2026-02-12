[![Latest Stable Version](https://poser.pugx.org/ladumor/laravel-pwa/v)](https://packagist.org/packages/ladumor/laravel-pwa)
[![Daily Downloads](https://poser.pugx.org/ladumor/laravel-pwa/d/daily)](https://packagist.org/packages/ladumor/laravel-pwa)
[![Monthly Downloads](https://poser.pugx.org/ladumor/laravel-pwa/d/monthly)](https://packagist.org/packages/ladumor/laravel-pwa)
[![Total Downloads](https://poser.pugx.org/ladumor/laravel-pwa/downloads)](https://packagist.org/packages/ladumor/laravel-pwa)
[![License](https://poser.pugx.org/ladumor/laravel-pwa/license)](https://packagist.org/packages/ladumor/laravel-pwa)
[![PHP Version Require](https://poser.pugx.org/ladumor/laravel-pwa/require/php)](https://packagist.org/packages/ladumor/laravel-pwa)

<a href="https://www.buymeacoffee.com/ladumor" target="_blank"><img src="https://cdn.buymeacoffee.com/buttons/v2/default-red.png" alt="Buy Me A Coffee" style="height: 60px !important;width: 217px !important;" ></a>

# Laravel PWA
## You can follow this video tutorial as well for installation.

[<img src="https://img.youtube.com/vi/9H-T81KQPyo/0.jpg" width="250">](https://youtu.be/9H-T81KQPyo)

## Watch Other Lavavel tutorial here
[<img src="https://img.youtube.com/vi/yMtsgBsqDQs/0.jpg" width="580">](https://www.youtube.com/channel/UCuCjzuwBqMqFdh0EU-UwQ-w?sub_confirmation=1))

## Installation

Install the package by the following command, (try without `--dev` if you want to install it on production environment)

    composer require --dev ladumor/laravel-pwa


## Add Provider

Add the provider to your `config/app.php` into `provider` section if using lower version of laravel,

    Ladumor\LaravelPwa\PWAServiceProvider::class,

## Add Facade

Add the Facade to your `config/app.php` into `aliases` section,

    'LaravelPwa' => \Ladumor\LaravelPwa\LaravelPwa::class,

## Publish the Assets

Run the following command to publish config file,

    php artisan laravel-pwa:publish

## Configure PWA
 Add following code in root blade file in header section.

    <!-- PWA  -->
    <meta name="theme-color" content="#6777ef"/>
    <link rel="apple-touch-icon" href="{{ asset('logo.png') }}">
    <link rel="manifest" href="{{ asset('/manifest.json') }}">

Add following code in root blade file in before close the body.

    @laravelPwa
    @pwaUpdateNotifier
    @pwaInstallButton

## Background Sync

This package supports Background Sync, allowing users to queue actions while offline and automatically synchronize them when the connection is restored.

### Automatic Form Sync
When a user submits a POST form while offline:
1. The submission is intercepted.
2. The data is saved locally in IndexedDB.
3. A background sync event is registered.
4. As soon as the browser detects the connection is restored, the Service Worker automatically retries the queued submissions.

### Manual Request Queuing
Developers can also manually queue requests using the global `window.laravelPwaSync` helper:

```javascript
const request = new Request('/api/data', {
    method: 'POST',
    body: JSON.stringify({ key: 'value' }),
    headers: {
        'Content-Type': 'application/json'
    }
});

if (window.laravelPwaSync) {
    await window.laravelPwaSync.queue(request);
}
```

### Browser Support
Background Sync relies on the `Service Worker` and `SyncManager` API. If the browser does not support these features, the application will continue to work as a standard web application without background synchronization.

## Smart Install Prompt Manager

This package provides helpers to manage the PWA installation experience effectively.

### Detecting Installability
You can check if the app is ready to be installed using `window.laravelPwaInstall.canInstall()`.

```javascript
if (window.laravelPwaInstall.canInstall()) {
    console.log('App is ready for installation');
}
```

### Deferring and Triggering Prompts
The package automatically intercepts the `beforeinstallprompt` event and defers it. You can trigger the prompt manually at a better time (e.g., after a user interaction).

```javascript
async function myCustomInstallFlow() {
    const outcome = await window.laravelPwaInstall.showPrompt();
    if (outcome === 'accepted') {
        console.log('User accepted the install prompt');
    }
}
```

### Tracking Install Events
You can listen for custom events to track the installation process:

```javascript
window.addEventListener('pwa-installable', (e) => {
    console.log('PWA is ready to be installed');
});

window.addEventListener('pwa-installed', () => {
    console.log('PWA was successfully installed');
});
```

### Checking Standalone Mode
Check if the app is currently running in standalone mode (installed):

```javascript
if (window.laravelPwaInstall.isStandalone()) {
    console.log('App is running in standalone mode');
}
```

### License
The MIT License (MIT). Please see [License](LICENSE.md) File for more information   


## Note
 PWA only works with https. so, you need to run either with  `php artisan serve` or create a virtual host with https.
 you can watch the video for how to create a virtual host with HTTPS

[<img src="https://img.youtube.com/vi/D5IqDcHyXSQ/0.jpg" width="550">](https://youtu.be/D5IqDcHyXSQ)


