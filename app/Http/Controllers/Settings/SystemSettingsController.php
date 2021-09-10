<?php
namespace App\Http\Controllers\Settings;

use App\Models\Settings\System_settings;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SystemSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:settings/system_settings-view')->only(['get']);
        $this->middleware('can:settings/system_settings-edit')->only(['update']);
    }

    public function get()
    {
        $system_settings = System_settings::get();

        $records = array();
        foreach ($system_settings as $system_setting) {
            $records[$system_setting->key_name] = $system_setting->value;
        }

        return response()->json([
            'message' => 'Get Record successfully',
            'record' => $records
        ], 201);
    }


    public function update(Request $request)
    {
        $records = $request->all();

        if (isset($records['images'])) {
            $images = $records['images'];
            foreach ($images as $key => $image) {
                if (!empty($image)) {
                    $records[$key] = $this->uploadfile_to_s3($image, 'ss_'.$key, 'system_settings');
                }
            }
            unset($records['images']);
        }

        foreach ($records as $key => $record) {
            System_settings::where('key_name', $key)
                ->update(['value' => $record]);
        }

        return response()->json([
            'message' => 'Record successfully updated'
        ], 201);
    }

}
