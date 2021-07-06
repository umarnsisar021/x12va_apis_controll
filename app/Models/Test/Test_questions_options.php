<?php

namespace App\Models\Test;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;



class Test_questions_options extends Model
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'id',
        'question_id',
        'type',
        'text',
        'is_correct'
    ];


    protected $hidden = [

    ];


    public static function rules($id)
    {
        return [
            'question_id' => 'required',
            'type' => 'required|string',
            'text' => 'required',
            'is_correct' => 'required'
        ];
    }
}