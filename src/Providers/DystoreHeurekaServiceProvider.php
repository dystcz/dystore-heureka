<?php

namespace Modules\DystoreHeureka\Providers;

use Filament\Panel;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Lunar\Admin\Filament\Resources\CollectionResource;
use Lunar\Admin\Filament\Resources\ProductResource;
use Lunar\Admin\Filament\Resources\ProductTypeResource;
use Lunar\Admin\Support\Facades\LunarPanel;
use Modules\DystoreHeureka\Commands\GenerateHeurekaFeed;
use Modules\DystoreHeureka\Domain\Collections\Filament\Resources\CollectionResourceExtension;
use Modules\DystoreHeureka\Domain\Products\Filament\Resources\ProductResourceExtension;
use Modules\DystoreHeureka\Domain\ProductTypes\Filament\Resources\ProductTypeResourceExtension;
use Nwidart\Modules\Traits\PathNamespace;

class DystoreHeurekaServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'DystoreHeureka';

    protected string $nameLower = 'dystore-heureka';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);

        LunarPanel::panel(
            fn (Panel $panel) => $panel,
        )->extensions([
            ProductResource::class => ProductResourceExtension::class,
            ProductTypeResource::class => ProductTypeResourceExtension::class,
            CollectionResource::class => CollectionResourceExtension::class,
        ])->register();
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateHeurekaFeed::class,
            ]);
        }
    }

    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules(): void
    {
        // $this->app->booted(function () {
        //     $schedule = $this->app->make(Schedule::class);
        //     $schedule->command('inspire')->hourly();
        // });
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/'.$this->nameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->nameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path($this->name, 'lang'), $this->nameLower);
            $this->loadJsonTranslationsFrom(module_path($this->name, 'lang'));
        }
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $this->publishes([module_path($this->name, 'config/config.php') => config_path($this->nameLower.'.php')], 'config');
        $this->mergeConfigFrom(module_path($this->name, 'config/config.php'), $this->nameLower);
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/'.$this->nameLower);
        $sourcePath = module_path($this->name, 'resources/views');

        $this->publishes([$sourcePath => $viewPath], ['views', $this->nameLower.'-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->nameLower);

        $componentNamespace = $this->module_namespace($this->name, $this->app_path(config('modules.paths.generator.component-class.path')));
        Blade::componentNamespace($componentNamespace, $this->nameLower);
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (is_dir($path.'/modules/'.$this->nameLower)) {
                $paths[] = $path.'/modules/'.$this->nameLower;
            }
        }

        return $paths;
    }
}
