<?php

namespace App\Models\Pages;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Tymon\JWTAuth\Contracts\JWTSubject;


class Pages extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
            'page_id',
            'value_key',
            'value',
            'options_list',
            'label',
            'type',
            'section',
            'col_width'
    ];
  
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        // 'password',
        // 'remember_token',
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
            'member_id' => 'required|string|between:2,100',
            'first_name' => 'required|string|between:2,50',
            'last_name' => 'required|string',
            'd_o_b' => 'required|string',
            'gender' => 'required|string',
            'email' => 'required|string',
            'mobile_number' => 'required|string',
            'country' => 'required|string',
            'region' => 'required|string',
            'score' => 'required|string',
            'info' => 'required|string',
            'status' => 'required|int'
        ];
    }
}