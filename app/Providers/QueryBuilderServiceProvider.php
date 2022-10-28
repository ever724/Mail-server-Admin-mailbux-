<?php

namespace App\Providers;

use App\Services\QueryBuilder\QueryBuilderRequest;
use Illuminate\Support\ServiceProvider;

class QueryBuilderServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(QueryBuilderRequest::class, function ($app) {
            return QueryBuilderRequest::fromRequest($app['request']);
        });
    }

    public function provides()
    {
        return [
            QueryBuilderRequest::class,
        ];
    }
}
