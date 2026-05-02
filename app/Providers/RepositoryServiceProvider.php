<?php

namespace App\Providers;

use App\Repositories\Contracts\InvitationRepositoryInterface;
use App\Repositories\Contracts\OrganizationRepositoryInterface;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\InvitationRepository;
use App\Repositories\Eloquent\OrganizationRepository;
use App\Repositories\Eloquent\ProjectRepository;
use App\Repositories\Eloquent\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $repositories = [
            OrganizationRepositoryInterface::class => OrganizationRepository::class,
            InvitationRepositoryInterface::class   => InvitationRepository::class,
            UserRepositoryInterface::class => UserRepository::class,
            ProjectRepositoryInterface::class => ProjectRepository::class,
        ];

        foreach($repositories as $interface => $repository) {
            $this->app->bind($interface, $repository);
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
