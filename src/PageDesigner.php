<?php

namespace SuperAdmin\Admin\PageDesigner;

use Illuminate\Support\Str;
use ReflectionClass;
use SuperAdmin\Admin\PageDesigner\Traits\PageDesignItem;
use Throwable;

class PageDesigner
{
    public $item_types = [];
    public $items = [];
    public $doc;
    public $config = [];
    public $script = [];
    public $page_designer_id;
    public $scripts = [];
    public $item_data;

    public function __construct($set_config = [])
    {
        $default = [
            'snap' => 40,
        ];
        $this->config = (array) PageDesignerExtension::config();
        $this->config = array_merge($default, $this->config, $set_config);
    }

    public function init($page_designer_id = null)
    {
        $this->page_designer_id = $page_designer_id;
        $this->collectPageDesignItems();
        $this->getItems();
    }

    public function setData($data = false)
    {
        $this->doc = $data;
    }

    public function getViewData($frontend = false)
    {
        if ($frontend) {
            $this->fixFrontendDoc();
        }

        return [
            'config'           => $this->config,
            'item_types'       => $this->item_types,
            'items'            => $this->items,
            'doc'              => $this->doc,
            'page_designer_id' => $this->page_designer_id,
            'scripts'          => $this->scripts,
        ];
    }

    /**
     * Prepare a saved document for the front end: add the aspect ratio and sort items top to bottom.
     */
    public function fixFrontendDoc()
    {
        if (empty($this->doc->settings)) {
            $this->doc = new \stdClass();
            $this->doc->settings = (object) [];
            $this->doc->settings->ratio = 1;
            $this->doc->items = [];

            return;
        }

        $settings = $this->doc->settings;
        $settings->ratio = empty($settings->width) ? 1 : round(($settings->height / $settings->width) * 100, 1);

        $items = (array) ($this->doc->items ?? []);

        usort($items, function ($a, $b) {
            return strnatcmp((string) ($a->y ?? ''), (string) ($b->y ?? ''));
        });

        $this->doc->items = $items;
    }

    /**
     * Find the controllers that provide block types.
     *
     * Set `admin.extensions.page-designer.item_controllers` to a list of classes,
     * or let the designer scan the admin controllers directory.
     *
     * @return array<int, string>
     */
    public function itemControllerClasses(): array
    {
        $configured = (array) PageDesignerExtension::config('item_controllers', []);

        if (! empty($configured)) {
            return array_values($configured);
        }

        $namespace = trim((string) config('admin.route.namespace', 'App\\Admin\\Controllers'), '\\');
        $directory = (string) config('admin.directory', app_path('Admin')).'/Controllers';

        $classes = [];
        foreach (glob($directory.'/*.php') ?: [] as $file) {
            $classes[] = $namespace.'\\'.basename($file, '.php');
        }

        return $classes;
    }

    public function collectPageDesignItems()
    {
        $this->item_types = [];
        $this->scripts = [];

        foreach ($this->itemControllerClasses() as $class) {
            if (! $this->classHasPageDignItemTrait($class)) {
                continue;
            }

            $model = str_replace('Controller', '', class_basename($class));

            $data = $class::pageDesign();
            $data['path'] = $data['path'] ?? Str::plural(Str::kebab($model));
            $data['class'] = $class;

            if (method_exists($class, 'pageDesignScripts')) {
                $this->scripts[] = $class::pageDesignScripts();
            }

            $this->item_types[$data['type']] = $data;
        }
    }

    public function classHasPageDignItemTrait($class_name): bool
    {
        try {
            if (! class_exists($class_name)) {
                return false;
            }

            return in_array(PageDesignItem::class, array_keys((new ReflectionClass($class_name))->getTraits()), true);
        } catch (Throwable $e) {
            // A controller that cannot be loaded is simply not a block type.
            return false;
        }
    }

    public function getItems()
    {
        $this->items = [];

        foreach ($this->item_types as $item_type) {
            $model = $item_type['model'];
            $rows = $model::where($item_type['parent_field'], $this->page_designer_id)->get()->toArray();

            $this->item_data[$item_type['type']] = [];
            foreach ($rows as $row) {
                $this->items[$item_type['type']][$row['id']] = $row;
            }
        }
    }
}
