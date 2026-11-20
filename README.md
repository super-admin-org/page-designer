PageDesigner for Super Admin
============================

Freely position items (text, images, video, galleries, embeds) on a page and edit their content, with a `pagedesigner` form field and a front-end renderer.

**Documentation:** https://super-admin.org/laravel/extension-page-designer/

Requirements: PHP 8.0+, Laravel 8 – 13, Super Admin 1.x, [super-admin-org/ckeditor](https://github.com/super-admin-org/ckeditor) (text blocks).

## Installation

### 1) Install and publish

```bash
composer require super-admin-org/page-designer
php artisan admin:import page-designer
php artisan vendor:publish --tag=page-designer    # assets + example controllers, models, migrations, views
php artisan migrate
```

The example files are published into `app/Admin/Controllers`, `app/Admin/Traits`, `app/Http/Controllers`, `app/Models`, `database/migrations` and `resources/views`. Publish them separately with `--tag=page-designer-assets` and `--tag=page-designer-example`. Review the result in Git before committing.

Until you add the routes below, `/admin/page-designer` shows a checklist of the remaining steps.

### 2) Add routes

`app/Admin/routes.php`:

```php
$router->resource('page-designer', PageDesignerController::class);
$router->resource('page-designer-images', PageDesignerImagesController::class);
$router->resource('page-designer-videos', PageDesignerVideoController::class);
$router->resource('page-designer-texts', PageDesignerTextController::class);
$router->resource('page-designer-inline-galleries', PageDesignerInlineGalleryController::class);
$router->resource('page-designer-embeds', PageDesignerEmbedController::class);
```

`routes/web.php`:

```php
use App\Http\Controllers\PageDesignerController;

Route::get('/page/{id}', [PageDesignerController::class, 'index'])->name('page-designer');
```

### 3) Use it

In a form:

```php
$form->pagedesigner('data', __('Page design'));

// Snap grid in pixels (default 40)
$form->pagedesigner('data')->snap(30);
```

Open `/page/{id}` to see the rendered page; change its look in `resources/views/page_designer.blade.php`.

## Adding new items

The designer looks for controllers that use the `PageDesignItem` trait in `admin.route.namespace` (default `App\Admin\Controllers`). To list them explicitly instead:

```php
// config/admin.php
'extensions' => [
    'page-designer' => [
        'item_controllers' => [
            App\Admin\Controllers\PageDesignerTextController::class,
            App\Admin\Controllers\PageDesignerQuoteController::class,
        ],
    ],
],
```

A quote item:

```php
use SuperAdmin\Admin\PageDesigner\Traits\PageDesignItem;

class PageDesignerQuoteController extends AdminController
{
    use PageDesignItem;

    public function __construct()
    {
        $this->initPageDesignItem();
    }

    public static function pageDesign()
    {
        return [
            'parent_field' => 'page_id',
            'type'         => 'quote',
            'title'        => 'quote',
            'icon'         => 'icon-quote-right',
            'model'        => \App\Models\PageDesignerQuote::class,
            // 'path'      => 'page-designer-quotes',   // admin resource path; defaults to the plural of the model name
        ];
    }

    // Renders the item's content in the editor (the front end renders separately).
    public static function pageDesignScripts()
    {
        return <<<'JS'
            // the type as written above + 'SetContent'
            window.quoteSetContent = function (data, current_content) {
                current_content.textContent = 'Quote: ' + data.quote;
            };
        JS;
    }
}
```

## Upgrading from 1.x

- The class `PageDesignerExtention` is now `PageDesignerExtension`; the old name still works.
- The package no longer registers `admin/page-designer` to a controller it did not ship (that broke `php artisan route:list`).

## Tests

```bash
composer install
composer test
```

License
-------
Licensed under [The MIT License (MIT)](LICENSE).
