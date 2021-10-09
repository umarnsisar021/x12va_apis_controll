<?php

namespace App\Models\Accounts;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;



class Transactions extends Model
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'id',
        'primary_table',
        'primary_id',
        'member_id',
        'type',
        'debit',
        'credit',
        'description',
        'trans_purpose',
        'status',
        'reference_no'
    ];


    protected $hidden = [

    ];


}