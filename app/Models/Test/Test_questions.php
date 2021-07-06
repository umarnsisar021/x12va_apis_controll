<?php

namespace App\Models\Test;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;



class Test_questions extends Model
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
        'question',
        'type',
        'status'
    ];


    protected $hidden = [

    ];


    public static function rules($id)
    {
        return [
            'skill_id' => 'required',
            'question' => 'required|string',
            'type' => 'required'
        ];
    }
}