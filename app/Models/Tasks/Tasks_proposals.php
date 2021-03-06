<?php

namespace App\Models\Tasks;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;



class Tasks_proposals extends Model
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
        'member_id',
        'subject',
        'problem_statement',
        'budget',
        'task_commission_from_client',
        'task_commission_from_expert',
        'verifier_commission_from_client',
        'verifier_commission_from_expert',
        'total_payable',
        'description',
        'status'
    ];


    protected $hidden = [

    ];


    public static function rules($id)
    {
        return [
            'task_id' => 'required',
            'member_id' => 'required',
            'budget' => 'required',
            'description' => 'required',
            'status' => 'required'
        ];
    }
}