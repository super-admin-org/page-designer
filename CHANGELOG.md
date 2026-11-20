# Changelog

## v1.0.6 - 2026-11-20

### Changed

- Works with super-admin 2.x: the package now requires `super-admin-org/super-admin` `^1.0|^2.0`. Earlier versions required `^1.0`, so Composer could not install them next to super-admin v2.0.0.
- Package homepage and documentation: https://super-admin.org/laravel/extension-page-designer/

## v1.0.5 - 2026-11-20

### Added

- Assets (not the example files) are also tagged `laravel-assets`, so Laravel apps republish them after every `composer update`.

## v1.0.4 - 2026-11-20

### Fixed

- The package registered `/admin/page-designer` and `/admin/page-designer/save` to `SuperAdmin\Admin\PageDesigner\PageDesignerController`, which did not exist: the page failed and `php artisan route:list` failed for the whole application. Those routes are gone; until the app adds its own route, `/admin/page-designer` shows the remaining install steps.
- Publishing copied `App/...` into the project root (capital `A`, not autoloaded on Linux). The example now goes to `app/Admin/Controllers`, `app/Admin/Traits`, `app/Http/Controllers`, `app/Models`, `database/migrations` and `resources/views`.
- Rendering a second document in one request failed with "Cannot redeclare build_sorter()".
- `PageDesignItem::show()` passed the wrong arguments to the parent controller.
- `->snap()` after `->config([...])` was ignored.
- A controller that could not be loaded stopped block discovery; it is skipped now.

### Added

- `item_controllers` option to list block controllers explicitly; discovery otherwise follows `admin.route.namespace` and `admin.directory`.
- Publish tags `page-designer-assets` and `page-designer-example` (`page-designer` still publishes both).
- Tests on Orchestra Testbench.

### Changed

- `PageDesignerExtention` is renamed `PageDesignerExtension`; the old name remains as an alias.
- The field's assets are declared on the field (`Form::collectFieldAssets()`), the standard way for Super Admin fields.
- Requires PHP 8.0+ and declares Laravel 8 – 13.
