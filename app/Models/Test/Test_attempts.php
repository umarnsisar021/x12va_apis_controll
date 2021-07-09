<?php

namespace App\Models\Test;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;



class Test_attempts extends Model
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'id',
        'test_id',
        'member_id',
        'start_time',
        'end_time',
        'status'
    ];


    protected $hidden = [

    ];


    public static function rules($id)
    {
        return [
            'test_id' => 'required',
            'member_id' => 'required',
            'start_time' => 'required',
            'end_time'=>'required'
        ];
    }
}