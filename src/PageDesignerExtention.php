<?php

namespace SuperAdmin\Admin\PageDesigner;

use SuperAdmin\Admin\Admin;
use SuperAdmin\Admin\Extension;

/**
 * Class PageDesigner.
 */
class PageDesignerExtention extends Extension
{
    /**
     * Boot PageDesigner.
     */
    public static function boot()
    {
        static::registerRoutes();

        Admin::extend('page-designer', __CLASS__);

        Admin::css('/vendor/super-admin-ext/page-designer/css/page-designer.css');
        Admin::css('/vendor/super-admin-ext/page-designer/js/resizeable.css');

        Admin::js('/vendor/super-admin-ext/page-designer/js/resizeable.js');
        Admin::js('/vendor/super-admin-ext/page-designer/js/interact.min.js');
        Admin::js('/vendor/super-admin-ext/page-designer/js/page-designer.js');
    }

    /**
     * Register routes for super-admin.
     *
     * @return void
     */
    protected static function registerRoutes()
    {
        parent::routes(function ($router) {
            $router->get('page-designer', 'SuperAdmin\Admin\PageDesigner\PageDesignerController@index')->name('page-designer-index');
            $router->post('page-designer/save', 'SuperAdmin\Admin\PageDesigner\PageDesignerController@save')->name('page-designer-save');
        });
    }

    /**
     * Import to menu.
     */
    public static function import()
    {
        parent::createMenu('Page Designer', 'page-designer', 'icon-object-group');

        parent::createPermission('Page designer', 'ext.page-designer', 'page-designer*');
    }
}
