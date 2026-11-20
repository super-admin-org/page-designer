<?php

namespace SuperAdmin\Admin\PageDesigner\Tests\fixtures;

use SuperAdmin\Admin\Controllers\AdminController;
use SuperAdmin\Admin\PageDesigner\Traits\PageDesignItem;

/**
 * A block type as an app would define it (see resources/example/App/Admin/Controllers).
 */
class TextBlockController extends AdminController
{
    use PageDesignItem;

    public static function pageDesign()
    {
        return [
            'parent_field' => 'page_id',
            'type'         => 'text',
            'title'        => 'Text',
            'icon'         => 'icon-align-left',
            'model'        => TextBlock::class,
        ];
    }
}
