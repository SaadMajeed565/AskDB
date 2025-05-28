<?php

namespace Dotknock\AskDb;

use Illuminate\Support\ServiceProvider;

class AskDbServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Merge default config file (optional)
        $this->mergeConfigFrom(__DIR__.'/../config/askdb.php', 'askdb');

        // Bind AskDb as singleton
        $this->app->singleton(AskDb::class, function ($app) {
            $askDb = new AskDb();

            // Load models from config and register
            $models = config('askdb.models', []);
            foreach ($models as $modelClass => $config) {
                $askDb->registerModel(
                    $modelClass,
                    $config['description'] ?? '',
                    $config['allowed_fields'] ?? [],
                    $config['disallowed_fields'] ?? []
                );
            }

            return $askDb;
        });

        // Alias for easier use (optional)
        $this->app->alias(AskDb::class, 'askdb');
    }

    public function boot()
    {
        // Publish config file for customization
        $this->publishes([
            __DIR__.'/../config/askdb.php' => config_path('askdb.php'),
        ], 'config');
    }
}
