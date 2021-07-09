<?php

namespace App\Models\Test;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;



class Test_attempts_answers extends Model
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'id',
        'attempt_id',
        'question_id',
        'option_id'
    ];


    protected $hidden = [

    ];


    public static function rules($id)
    {
        return [
            'attempt_id' => 'required',
            'question_id' => 'required',
            'option_id' => 'required'
        ];
    }
}