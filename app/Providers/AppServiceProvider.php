<?php

namespace App\Providers;

use App\Renderers\Set\ResponseListRenderer;
use App\Renderers\Set\ResponsesRendererInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ResponsesRendererInterface::class, ResponseListRenderer::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $log_sql = config('app.log_sql_queries', false);
        if ($log_sql) {
            DB::listen(function ($query) {
                Log::debug('sql query: ' . print_r([
                        $query->sql,
                        $query->bindings,
                        $query->time,
                    ], true));
            });
        }
    }
}
