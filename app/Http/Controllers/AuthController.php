<?php

namespace App\Http\Controllers;
use App\Models\Settings\Roles\Modules;
use App\Models\Settings\Roles\Modules_permissions;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\JWTAuth;

use Validator;
use Illuminate\Support\Facades\Gate;


class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['register','authenticate']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */


    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');
        try {
            if (! $token = \JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
        return $this->createNewToken($token);
    }

    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
            'role' => 'required|string|between:2,100',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }


    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'User successfully signed out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->createNewToken(auth()->refresh());
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile()
    {
        return response()->json(auth()->user());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token)
    {
        $userData = auth()->user();
//        $userData['ability'] = [["action" => "manage", "subject" => "all"]];//admin
//        $userData['ability']=[["action"=>"view", "subject"=> "dashboard/analytics"],["action"=>"read", "subject"=> "Auth"],["action"=>"view", "subject"=> "app"]];//users


        $access_rights=Modules_permissions::where('role_id',$userData->role_id)
            ->select('roles_modules.slug as subject','roles_modules_actions.action as action')
            ->leftjoin('roles_modules_actions', 'roles_modules_actions.id', '=', 'roles_modules_permissions.action_id')
            ->leftjoin('roles_modules', 'roles_modules.id', '=', 'roles_modules_permissions.module_id')->get()->toarray();

        $userData['navigation'] = $this->navigation();
        $userData['ability'] = $access_rights;
//        Auth::user()->setAttribute('ability',$access_rights);
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => $userData
        ]);
    }


    public function navigation()
    {
        $navigation = [];
        $modules_parents = Modules::where('parent_id', 0)
            ->where('status', 1)
            ->select('*')
            ->get();

        foreach ($modules_parents as $modules_parent) {
            $modules_childs = Modules::where('parent_id', $modules_parent->id)
                ->where('status', 1)
                ->select('*')
                ->get();

            $children = [];
            foreach ($modules_childs as $modules_child) {
                $children[] = [
                    'id' => $modules_child->id,
                    'title' => $modules_child->name,
                    'icon' => $modules_child->icon,
                    'badge' => 'light-warning',
                    'navLink' => '/'.$modules_child->slug.'/list',//'/dashboard/analytics',
                    'action' => 'view',
                    'resource' => $modules_child->slug
                ];
            }

            $nav=[
                'id' => $modules_parent->id,
                'title' => $modules_parent->name,
                'icon' => $modules_parent->icon,
                'badge' => 'light-warning',
                'navLink' => '/'.$modules_parent->slug.'/list',//'/dashboard/analytics',
                'action' => 'view',
                'resource' => $modules_parent->slug,

            ];
            if(count($children)>0){
                $nav['children']=$children;
            }
            $navigation[] = $nav;
        }
        return $navigation;
    }
}