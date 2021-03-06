<?php

namespace App\Models;

use App\Models\Settings\Roles\Modules_permissions;
use App\Models\Settings\Roles\Roles;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Tymon\JWTAuth\Contracts\JWTSubject;


class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'avatar'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier() {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims() {
        return [];
    }


    public static function rules($id)
    {
        return [
            'name' => 'required|string|between:2,100',
            'email' => 'unique:users,email,' . $id . '|required',
            'password' => 'required|string|confirmed|min:6',
            'role_id' => 'required'
        ];
    }


    public function setRoleAttribute($key,$value)
    {
        $this->attributes[$key] = strtolower($value);
    }


    public function role()
    {
        return $this->hasOne(Roles::class,'id','role_id');
    }
}