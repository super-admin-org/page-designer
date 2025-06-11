<?php

namespace SuperAdmin\Admin\PageDesigner;

use Illuminate\Support\ServiceProvider;
use SuperAdmin\Admin\Admin;
use SuperAdmin\Admin\Form;

class PageDesignerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        PageDesignerExtention::boot();

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'super-admin-page-designer');

        if ($this->app->runningInConsole()) {
            $this->publishes(
                [
                    __DIR__.'/../resources/assets/'  => public_path('vendor/super-admin-ext/page-designer/'),
                    __DIR__.'/../resources/example/' => base_path(''),
                ],
                'page-designer'
            );
        }

        Admin::booting(function () {
            Form::extend('pagedesigner', PageDesignerField::class);
        });
    }
}
