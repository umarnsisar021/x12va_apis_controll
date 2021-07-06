<?php

namespace App\Models\Tasks;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;



class Tasks_status_histories extends Model
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'id',
        'task_id',
        'status',
        'member_id'
    ];


    protected $hidden = [

    ];


    public static function rules($id)
    {
        return [
            'task_id' => 'required',
            'status' => 'required',
            'member_id' => 'required'
        ];
    }
}