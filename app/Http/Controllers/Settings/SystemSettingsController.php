<?php
namespace App\Http\Controllers\Settings;

use App\Models\Members\Members;
use App\Models\Settings\System_settings;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

class SystemSettingsController extends Controller
{
    public function __construct()
    {
        parent::__construct();
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



    public function get_company_config(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required'
        ]);

        if ($validator->fails()) {
            $validators = $validator->errors()->toArray();
            $data = [
                'validations' => $validators,
                'message' => $validator->errors()->first()
            ];
            return response()->json($data, 400);
        }


        $token = $request->token;
        $member_record = Members::where('token', '=', $token)->first();
        if (!$member_record || empty($token)) {
            return response()->json([
                'message' => 'invalid token',
                'status' => 405
            ], 400);
        }

        $data=System_settings::pluck('value','key_name');
        if ($data) {
            return response()->json([
                'message' => 'Get Config',
                'data'=>$data,
                'status' => 201
            ], 201);
        } else {
            return response()->json([
                'message' => 'Some thing wrong',
                'status' => 400
            ], 400);
        }


    }

}
