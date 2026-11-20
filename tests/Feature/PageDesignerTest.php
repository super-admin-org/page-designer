<?php

namespace SuperAdmin\Admin\PageDesigner\Tests\Feature;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use SuperAdmin\Admin\Form;
use SuperAdmin\Admin\PageDesigner\PageDesigner;
use SuperAdmin\Admin\PageDesigner\PageDesignerExtension;
use SuperAdmin\Admin\PageDesigner\PageDesignerField;
use SuperAdmin\Admin\PageDesigner\PageDesignerServiceProvider;
use SuperAdmin\Admin\PageDesigner\Tests\fixtures\TextBlockController;
use SuperAdmin\Admin\PageDesigner\Tests\TestCase;

class PageDesignerTest extends TestCase
{
    public function test_no_route_points_to_a_missing_controller(): void
    {
        foreach (Route::getRoutes() as $route) {
            $action = $route->getActionName();

            if ($action === 'Closure' || ! str_contains($action, 'PageDesigner')) {
                continue;
            }

            $this->assertTrue(class_exists(explode('@', $action)[0]), "Route {$route->uri()} points to missing {$action}");
        }

        $this->assertFalse(Route::has('page-designer-index'));
    }

    public function test_setup_page_is_shown_until_the_app_routes_the_designer(): void
    {
        $this->get('admin/page-designer')
            ->assertOk()
            ->assertSee('Finish installing the Page Designer')
            ->assertSee('php artisan vendor:publish --tag=page-designer');

        $this->assertTrue(PageDesignerServiceProvider::hasPageDesignerRoute());
    }

    public function test_route_list_works_with_the_extension_installed(): void
    {
        $this->artisan('route:list')->assertExitCode(0);
    }

    public function test_the_old_class_name_still_works(): void
    {
        $this->assertTrue(class_exists('SuperAdmin\Admin\PageDesigner\PageDesignerExtention'));
        $this->assertInstanceOf(PageDesignerExtension::class, new \SuperAdmin\Admin\PageDesigner\PageDesignerExtention());
    }

    public function test_front_end_documents_can_be_prepared_twice(): void
    {
        $make = function () {
            $designer = new PageDesigner();
            $designer->setData((object) [
                'settings' => (object) ['width' => 960, 'height' => 480],
                'items'    => [(object) ['y' => 200, 'id' => 'b'], (object) ['y' => 40, 'id' => 'a']],
            ]);

            return $designer->getViewData(true)['doc'];
        };

        $first = $make();
        $second = $make();

        $this->assertSame(50.0, $second->settings->ratio);
        $this->assertSame(['a', 'b'], array_column(array_map('get_object_vars', $first->items), 'id'));
    }

    public function test_an_empty_document_gets_defaults(): void
    {
        $designer = new PageDesigner();
        $designer->setData(null);

        $doc = $designer->getViewData(true)['doc'];

        $this->assertSame(1, $doc->settings->ratio);
        $this->assertSame([], $doc->items);
    }

    public function test_block_types_can_be_configured_and_broken_classes_are_skipped(): void
    {
        config(['admin.extensions.page-designer.item_controllers' => [
            TextBlockController::class,
            'App\Admin\Controllers\DoesNotExist',
        ]]);

        $designer = new PageDesigner();
        $designer->collectPageDesignItems();

        $this->assertSame(['text'], array_keys($designer->item_types));
        $this->assertSame('text-blocks', $designer->item_types['text']['path']);
    }

    public function test_snap_can_be_changed_after_other_options(): void
    {
        $field = new PageDesignerField('data');
        $field->config(['snap' => 10, 'grid' => true])->snap(30);

        $this->assertSame(30, $field->config['snap']);
        $this->assertTrue($field->config['grid']);
    }

    public function test_field_assets_are_collected_for_every_page(): void
    {
        $assets = PageDesignerField::getAssets();

        $this->assertContains('/vendor/super-admin-org/page-designer/js/page-designer.js', $assets['js']);
        $this->assertContains('/vendor/super-admin-org/page-designer/css/page-designer.css', $assets['css']);

        app(\SuperAdmin\Admin\Admin::class)->bootstrap();
        $this->assertSame(PageDesignerField::class, Form::findFieldClass('pagedesigner'));
    }

    public function test_the_example_is_published_into_app_not_the_project_root(): void
    {
        $paths = ServiceProvider::pathsToPublish(PageDesignerServiceProvider::class, 'page-designer-example');

        $this->assertContains(app_path('Models'), $paths);
        $this->assertContains(app_path('Admin/Controllers'), $paths);
        $this->assertNotContains(base_path(''), $paths);
        $this->assertNotContains(base_path(), $paths);

        $assets = ServiceProvider::pathsToPublish(PageDesignerServiceProvider::class, 'page-designer-assets');
        $this->assertSame([public_path('vendor/super-admin-org/page-designer/')], array_values($assets));
    }
}
