<?php

namespace SuperAdmin\Admin\PageDesigner\Traits;

use SuperAdmin\Admin\Facades\Admin;
use SuperAdmin\Admin\Form;
use SuperAdmin\Admin\Layout\Content;

//addBodyClass
trait PageDesignItem
{
    public function initPageDesignItem()
    {
        $this->hook('alterForm', function ($scope, $form) {
            Admin::css('/vendor/super-admin-org/page-designer/css/page-designer-modal.css', false);
            $form = $this->addPageDesigner($form);

            return $form;
        });
    }

    public function index(Content $content)
    {
        $content->addBodyClass('hide-nav  white-bg');

        return parent::index($content);
    }

    public function show($id, Content $content)
    {
        $content->addBodyClass('hide-nav  white-bg');

        return parent::show($id, $content);
    }

    public function edit($id, Content $content)
    {
        $content->addBodyClass('hide-nav  white-bg');

        return parent::edit($id, $content);
    }

    public function create(Content $content)
    {
        $content->addBodyClass('hide-nav white-bg');

        return parent::create($content);
    }

    public function addPageDesigner($form)
    {
        $settings = self::pageDesign();

        $form->hidden($settings['parent_field'], 'parent')
             ->value(request()->input($settings['parent_field']));

        $form->saved(function (Form $form) {
            $model = $form->model();

            return response('<script>window.top.updateItem('.json_encode($model).');</script>');
        });

        return $form;
    }
}
