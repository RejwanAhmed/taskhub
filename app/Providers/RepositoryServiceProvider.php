<?php

namespace App\Providers;

use App\Repositories\Contracts\InvitationRepositoryInterface;
use App\Repositories\Contracts\OrganizationRepositoryInterface;
use App\Repositories\Eloquent\InvitationRepository;
use App\Repositories\Eloquent\OrganizationRepository;
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
