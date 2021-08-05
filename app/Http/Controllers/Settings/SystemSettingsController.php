<?php
namespace App\Http\Controllers\Settings;

use App\Models\Settings\System_settings;
use App\Http\Controllers\Controller;
use Validator;

class SystemSettingsController extends Controller
{



    public function get()
    {
       $system_settings= System_settings::get();

        $records=array();
        foreach($system_settings as $system_setting){
            $records[$system_setting->key_name]=$system_setting->value;
        }

        return response()->json([
            'message' => 'Get Record successfully',
            'record'=>$records
        ], 201);
    }



}
