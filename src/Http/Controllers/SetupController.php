<?php

namespace SuperAdmin\Admin\PageDesigner\Http\Controllers;

use Illuminate\Routing\Controller;
use SuperAdmin\Admin\Layout\Content;

/**
 * Shown at /admin/page-designer until the application registers its own page designer routes.
 */
class SetupController extends Controller
{
    public function index(Content $content): Content
    {
        $namespace = trim((string) config('admin.route.namespace', 'App\\Admin\\Controllers'), '\\');

        $checks = [
            'Example controllers published' => class_exists($namespace.'\\PageDesignerController'),
            'Example models published'      => class_exists('App\\Models\\PageDesigner'),
            'Assets published'              => is_file(public_path('vendor/super-admin-org/page-designer/js/page-designer.js')),
        ];

        return $content
            ->title('Page Designer')
            ->description('Finish installing')
            ->body(view('super-admin-page-designer::setup', ['checks' => $checks]));
    }
}
