<?php

namespace App\Models\Tasks;

use App\Models\App\Skills;
use App\Models\Clients\Clients;
use App\Models\Experts\Experts;
use App\Models\Members\Members;
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




    public function experts(){
        return $this->hasOne(Experts::class,'member_id','expert_id');
    }

    public function getExpertAttribute()
    {
        $expert = $this->experts();
        return $expert;
    }



    public function clients(){
        return $this->hasOne(Clients::class,'member_id','expert_id');
    }

    public function getClientAttribute()
    {
        $client = $this->clients();
        return $client;
    }



}