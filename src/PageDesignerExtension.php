<?php

namespace SuperAdmin\Admin\PageDesigner;

use SuperAdmin\Admin\Admin;
use SuperAdmin\Admin\Extension;

/**
 * Registers the page designer extension.
 *
 * The admin pages themselves come from the published example controllers
 * (`php artisan vendor:publish --tag=page-designer-example`) and the routes
 * you add to app/Admin/routes.php, so the package registers no routes of its own.
 */
class PageDesignerExtension extends Extension
{
    public $name = 'page-designer';

    /**
     * Boot PageDesigner.
     */
    public static function boot()
    {
        Admin::extend('page-designer', __CLASS__);
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
