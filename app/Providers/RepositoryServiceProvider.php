<?php

namespace Lockd\Providers;

use Illuminate\Support\ServiceProvider;
use Lockd\Repositories\DefaultFolderRepository;
use Lockd\Repositories\DefaultGroupRepository;
use Lockd\Repositories\DefaultPasswordRepository;
use Lockd\Repositories\DefaultUserRepository;
use Lockd\Services\PermissionManager;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(\Lockd\Contracts\Repositories\UserRepository::class, function () {
            return new DefaultUserRepository();
        });

        $this->app->bind(\Lockd\Contracts\Repositories\GroupRepository::class, function () {
            return new DefaultGroupRepository();
        });

        $this->app->bind(\Lockd\Contracts\Repositories\FolderRepository::class, function () {
            return new DefaultFolderRepository(new PermissionManager());
        });

        $this->app->bind(\Lockd\Contracts\Repositories\PasswordRepository::class, function () {
            return new DefaultPasswordRepository();
        });
    }
}
