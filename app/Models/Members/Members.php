<?php

namespace App\Models\Members;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Tymon\JWTAuth\Contracts\JWTSubject;


class Members extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'is_buyer',
        'is_seller',
        'username',
        'password',
        'email',
        'mobile_no',
        'status',
        'google_id',
        'facebook_id',
        'twitter_id',
        'signup_with',
        'token'
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
    // protected $casts = [
    //     'email_verified_at' => 'datetime',
    // ];

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
            'username' => 'required|string|between:2,100|unique:members',
            'password' => 'required|string',
            'email' => 'required|string',
            'mobile_no' => 'required|string',
            'status' => 'required|int'
        ];
    }




    public function transactions()
    {
        return $this->hasMany('App\Models\Accounts\Transactions','member_id','id')->where("status",1);
    }

    public function getBalanceAttribute(){
        return $this->transactions->sum(function($trans){
            return $trans->debit - $trans->credit;
        });
    }
}