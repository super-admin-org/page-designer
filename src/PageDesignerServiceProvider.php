<?php

namespace SuperAdmin\Admin\PageDesigner;

use Illuminate\Support\ServiceProvider;
use SuperAdmin\Admin\Admin;
use SuperAdmin\Admin\Form;
use SuperAdmin\Admin\PageDesigner\Http\Controllers\SetupController;

class PageDesignerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        PageDesignerExtension::boot();

        // Template-aware: <views>/<template>/ and <template>/extensions/super-admin-page-designer/ override <views>/.
        if (class_exists(\SuperAdmin\Admin\Template\TemplateManager::class)) {
            \SuperAdmin\Admin\Facades\Admin::template()->extensionViews('super-admin-page-designer', __DIR__.'/../resources/views');
        } else {
            $this->loadViewsFrom(__DIR__.'/../resources/views', 'super-admin-page-designer');
        }

        if ($this->app->runningInConsole()) {
            $assets = [__DIR__.'/../resources/assets/' => public_path('vendor/super-admin-org/page-designer/')];

            // Each example folder goes to its real place (app/, not App/), nothing lands in the project root.
            $example = __DIR__.'/../resources/example';
            $exampleFiles = [
                $example.'/App/Admin/Controllers' => app_path('Admin/Controllers'),
                $example.'/App/Admin/Traits'      => app_path('Admin/Traits'),
                $example.'/App/Http/Controllers'  => app_path('Http/Controllers'),
                $example.'/App/Models'            => app_path('Models'),
                $example.'/database/migrations'   => database_path('migrations'),
                $example.'/resources/views'       => resource_path('views'),
            ];

            $this->publishes($assets, ['page-designer-assets', 'laravel-assets']);
            $this->publishes($exampleFiles, 'page-designer-example');
            $this->publishes($assets + $exampleFiles, 'page-designer');
        }

        Admin::booting(function () {
            Form::extend('pagedesigner', PageDesignerField::class);
        });

        // Until the app routes /admin/page-designer itself, show the remaining install steps there.
        $this->app->booted(function () {
            if (! static::hasPageDesignerRoute()) {
                PageDesignerExtension::routes(function ($router) {
                    $router->get('page-designer', [SetupController::class, 'index'])->name('page-designer-setup');
                });
            }
        });
    }

    public static function hasPageDesignerRoute(): bool
    {
        $uri = trim(config('admin.route.prefix', 'admin').'/page-designer', '/');
        $routes = app('router')->getRoutes()->getRoutesByMethod()['GET'] ?? [];

        return isset($routes[$uri]);
    }
}
