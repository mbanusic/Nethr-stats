<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

	    Gate::define('update-user', function ($user, $other) {
	    	if (in_array($user->id, [36, 38])) { //allow "admins" to update users
	    		return true;
		    }
			//allow users to update their profiles
		    return $user->id == $other->id;
	    });
    }
}
