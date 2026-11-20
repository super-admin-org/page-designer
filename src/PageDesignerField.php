<?php

namespace SuperAdmin\Admin\PageDesigner;

use SuperAdmin\Admin\Form\Field\Textarea;

class PageDesignerField extends Textarea
{
    public $config = [];

    protected $view = 'super-admin-page-designer::index';

    /**
     * Collected by Form::collectFieldAssets() for every admin page, so the field also works after PJAX navigation.
     *
     * @var array<int, string>
     */
    protected static $css = [
        '/vendor/super-admin-org/page-designer/css/page-designer.css',
        '/vendor/super-admin-org/page-designer/js/resizeable.css',
    ];

    /**
     * @var array<int, string>
     */
    protected static $js = [
        '/vendor/super-admin-org/page-designer/js/resizeable.js',
        '/vendor/super-admin-org/page-designer/js/interact.min.js',
        '/vendor/super-admin-org/page-designer/js/page-designer.js',
    ];

    public function snap($pixels = 40)
    {
        return $this->config(['snap' => intval($pixels)]);
    }

    public function config($config = [])
    {
        $this->config = array_merge($this->config, $config);

        return $this;
    }

    public function render()
    {
        $config = array_merge($this->config, ['field' => $this->column]);

        $pageDesigner = new PageDesigner($config);
        $pageDesigner->init(optional($this->form->model())->getKey());
        $pageDesigner->setData($this->value);

        view()->share($pageDesigner->getViewData());
        view()->share('is_field', true);

        return parent::render();
    }
}
