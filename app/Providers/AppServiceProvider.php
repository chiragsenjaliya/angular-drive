<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\ServiceProvider;
use App\Components\User\Contracts\IUserRepository;
use App\Components\User\Repositories\MySQLUserRepository;
use App\Components\User\Contracts\ICompanyRepository;
use App\Components\User\Repositories\MySQLCompanyRepository;
use App\Components\FolderFile\Contracts\IFolderRepository;
use App\Components\FolderFile\Repositories\MySQLFolderRepository;
use Route;
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
              
        Schema::defaultStringLength(191);

        Resource::withoutWrapping();
        
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
        $this->app->bind(IFolderRepository::class, MySQLFolderRepository::class);
    }
}
