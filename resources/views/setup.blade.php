<div class="card">
    <div class="card-header">
        <h3 class="card-title">Finish installing the Page Designer</h3>
    </div>
    <div class="card-body">
        <p>The page designer pages come from example controllers that you publish into your application and route yourself.</p>

        <ul class="list-group mb-4">
            @foreach($checks as $label => $done)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    {{ $label }}
                    <span class="badge {{ $done ? 'bg-success' : 'bg-warning' }}">{{ $done ? 'done' : 'to do' }}</span>
                </li>
            @endforeach
        </ul>

        <h4>1. Publish the example and the assets, then migrate</h4>
        <pre><code>php artisan vendor:publish --tag=page-designer
php artisan migrate</code></pre>

        <h4>2. Add the admin routes to <code>app/Admin/routes.php</code></h4>
        <pre><code>$router->resource('page-designer', PageDesignerController::class);
$router->resource('page-designer-images', PageDesignerImagesController::class);
$router->resource('page-designer-videos', PageDesignerVideoController::class);
$router->resource('page-designer-texts', PageDesignerTextController::class);
$router->resource('page-designer-inline-galleries', PageDesignerInlineGalleryController::class);
$router->resource('page-designer-embeds', PageDesignerEmbedController::class);</code></pre>

        <h4>3. Add the front-end route to <code>routes/web.php</code></h4>
        <pre><code>Route::get('/page/{id}', [App\Http\Controllers\PageDesignerController::class, 'index'])->name('page-designer');</code></pre>

        <p class="text-muted small mb-0">This page disappears once your own <code>page-designer</code> route exists.</p>
    </div>
</div>
