<?php

namespace App\Providers;

use App\Models\Settings\Roles\Modules_permissions;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        if (auth::check()) {
            $permissions = Modules_permissions::with('actions', 'modules')->where('role_id', auth()->user()->role_id)->get()->toArray();

            foreach ($permissions as $index => $permission) {
//            print_r($permission['modules']['slug'] . '-' . $permission['actions']['action']);die;
                Gate::define($permission['modules']['slug'] . '-' . $permission['actions']['action'], function ($user) use ($permission) {
                    return $user;
//                return $user->hasPermission($permission);
                });
            }
        }
//        Gate::define('dgs',function($user){
//            print_r($user);die;
//         return $user;
//        }
//        );
        //
    }
}
