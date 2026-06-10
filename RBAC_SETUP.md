// Add this to the $routeMiddleware array in app/Http/Kernel.php

protected $routeMiddleware = [
    // ... existing middleware
    'perm' => \App\Http\Middleware\CheckPermission::class,
];
