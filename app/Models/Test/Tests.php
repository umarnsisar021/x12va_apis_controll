<?php

namespace App\Models\Test;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;



class Tests extends Model
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'id',
        'skill_id',
        'name',
        'description',
        'duration',
        'status'
    ];


    protected $hidden = [

    ];


    public static function rules($id)
    {
        return [
            'skill_id' => 'required',
            'name' => 'required|string',
            'duration' => 'required'
        ];
    }
}