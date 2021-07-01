<?php

namespace App\Models\Tasks;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;



class Tasks extends Model
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'id',
        'client_id',
        'assigned_to',
        'description',
        'document',
        'days',
        'status',
        'skill_id'
    ];


    protected $hidden = [

    ];


    public static function rules($id)
    {
        return [
            'client_id' => 'required',
            'description' => 'required|string',
            'days' => 'required',
            'skill_id'=>'required'
        ];
    }
}