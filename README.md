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
    @pwaHead

You can also pass custom logo and theme color as arguments:

    @pwaHead('custom-logo.png', '#ff0000')

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

## Splash Screen & Icon Generator

This package includes a tool to automatically generate all required PWA icons and splash screen assets from a single source image.

### How to use:
1. Place your high-resolution source image (recommended 512x512 or larger) in the `public` directory of your Laravel application.
2. Name the file `pwa-source.png`.
3. Run the publish command:
   ```bash
   php artisan laravel-pwa:publish
   ```
4. The package will automatically generate icons of various sizes (72x72 to 512x512) and update your `manifest.json`.

If `pwa-source.png` is not found, the package will use a default logo to generate the assets.

### Smart Versioning & Cache Busting

This package includes a smart versioning system to ensure that your users always have the latest version of your PWA and its assets.

#### How it works:
1.  **Service Worker Versioning**: Every time you run `php artisan laravel-pwa:publish`, a new unique version ID is generated and embedded in your `sw.js`. This triggers an update in the user's browser.
2.  **Manifest & Icon Cache Busting**: The `manifest.json` and all listed icons include version query parameters, ensuring that browsers and devices refresh them when you re-publish.
3.  **Blade Directive versioning**: The `@laravelPwa` directive automatically appends a version timestamp to the `sw.js`, `pwa-install.js`, and `background-sync.js` script registrations. This forces the browser to check for a new Service Worker file immediately.
4.  **Automatic Update Notification**: When a new version is detected, the `@pwaUpdateNotifier` directive (if included) will automatically handle the `skipWaiting` and reload the page to activate the new Service Worker, ensuring a seamless update experience for users.

To update your PWA version and bust all caches, simply run:
```bash
php artisan laravel-pwa:publish
```

## Built-in Manifest Generator

Instead of manually editing the `manifest.json` file, you can use the interactive manifest generator to configure your PWA settings:

```bash
php artisan pwa:manifest
```

This command will prompt you for:
- App Name
- Short Name
- Description
- Start URL
- Theme & Background Colors
- Display Mode (fullscreen, standalone, etc.)
- Orientation

After running this command, remember to run `php artisan laravel-pwa:publish` to apply the changes to your public directory.

## Debug & Dev Tools

This package includes a set of developer-friendly tools to help you debug your PWA implementation.

### Artisan Command
Run the following command to see debugging tips:
```bash
php artisan pwa:debug
```

### On-Screen Debug Helper
Add the `@pwaDebug` directive to your blade file (ideally only in development). It will only load if `APP_DEBUG=true` in your `.env`.

```blade
@pwaDebug
```

This directive injects a global `window.laravelPwaDebug` object into your browser console with the following utilities:

| Method | Description |
| --- | --- |
| `laravelPwaDebug.viewCaches()` | List all PWA caches and their current contents. |
| `laravelPwaDebug.clearCaches()` | Clear all PWA-related caches. |
| `laravelPwaDebug.forceUpdate()` | Force a Service Worker update check. |
| `laravelPwaDebug.unregister()` | Unregister all Service Workers for the domain. |
| `laravelPwaDebug.help()` | Show available debug commands. |

### Service Worker Logging
The Service Worker now includes basic lifecycle logging (Installing, Activated) to help you track its state in the browser console.

### License
The MIT License (MIT). Please see [License](LICENSE.md) File for more information   


## Note
 PWA only works with https. so, you need to run either with  `php artisan serve` or create a virtual host with https.
 you can watch the video for how to create a virtual host with HTTPS

[<img src="https://img.youtube.com/vi/D5IqDcHyXSQ/0.jpg" width="550">](https://youtu.be/D5IqDcHyXSQ)


