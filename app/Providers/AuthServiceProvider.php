<?php

namespace App\Providers;

use App\Models\Message;
use App\Models\User;
use App\Policies\MessagePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Message::class => MessagePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define Gate for admin dashboard access
        Gate::define('view-admin-dashboard', function (User $user) {
            return $user->role === 'admin';
        });

        // Define Gate for suspending users
        Gate::define('suspend-users', function (User $user) {
            return $user->role === 'admin';
        });

        // Define Gate for deleting any message
        Gate::define('delete-any-message', function (User $user) {
            return $user->role === 'admin';
        });

        // Define Gate for deleting any story
        Gate::define('delete-any-story', function (User $user) {
            return $user->role === 'admin';
        });
    }
}
