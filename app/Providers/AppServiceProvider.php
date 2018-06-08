<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\ServiceProvider;
use App\Components\User\Contracts\IUserRepository;
use App\Components\User\Repositories\MySQLUserRepository;
use App\Components\User\Contracts\ICompanyRepository;
use App\Components\User\Repositories\MySQLCompanyRepository;
use Route;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Passport::routes();
        
        Schema::defaultStringLength(191);

        Resource::withoutWrapping();

        // Middleware `api` that contains the `custom-provider` middleware group defined on $middlewareGroups above
        Route::group(['middleware' => 'api'], function () {
            Passport::routes(function ($router) {
                return $router->forAccessTokens();
            });
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // bindings
        $this->app->bind(IUserRepository::class, MySQLUserRepository::class);
        $this->app->bind(ICompanyRepository::class, MySQLCompanyRepository::class);
    }
}
