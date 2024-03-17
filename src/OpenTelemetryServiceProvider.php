<?php

namespace OpenTelemetryManager;

use Illuminate\Support\ServiceProvider;

class OpenTelemetryServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/config/open-telemetry-manager.php' => config_path('open-telemetry-manager.php'),
            ], 'config');
        }

        $this->mergeConfigFrom(
            __DIR__.'/config/open-telemetry-manager.php', 'open-telemetry-manager'
        );

    }

    public function register()
    {

    }

}