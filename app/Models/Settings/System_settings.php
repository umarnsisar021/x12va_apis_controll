<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;



class System_settings extends Model
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'id',
        'key_name',
        'value'
    ];


    protected $hidden = [

    ];


    public static function getSystemSetting($key){
        $global_settings= self::where('key_name',$key)->select('value')->first();
        if(isset($global_settings->value)){
            return $global_settings->value;
        }
        return '';
    }

    public static function setSystemSetting($key,$value){
        $global_settings= self::where('key_name',$key)->find(['value'=>$value]);
        if($global_settings){
            return true;
        }
        return false;
    }
}