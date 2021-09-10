<?php
namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pages\Pages;
use App\Models\Pages\PagesField;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Validator;

class HomePageController extends Controller
{
    protected $global;

    public function __construct()
    {
        $this->global = config('app.global');
        $this->middleware('can:dashboard/analytics-view')->only(['get_data', 'get']);
    }

    public function update_banner(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|int'
        ]);


        if ($validator->fails()) {
            $validators = $validator->errors()->toArray();
            $data = [
                'validations' => $validators,
                'message' => $validator->errors()->first()
            ];
            return response()->json($data, 400);
        }
        $value = array();
        if ($request['banner_pre']) {
            foreach ($request['banner_pre'] as $key => $v) {
                if ($request['heading'][$key]) {
                    $heading = $request['heading'][$key];
                    $text = $request['text'][$key];;
                    $image = $request['banner_pre'][$key];;
                    if ($request['banner_new'][$key]) {
                        $banner_name = 'home_banner_' . $key;
                        $image = $this->uploadfile_to_s3($request['banner_new'][$key], $banner_name, 'banners');
                    }
                    $array = array(
                        'heading' => $heading,
                        'text' => $text,
                        'image' => $image
                    );
                    array_push($value, $array);
                }
            }
        }
        $value = json_encode($value);
        $fields = PagesField::where('id', '=', $request->id)
            ->update(['value' => $value]);

        return response()->json([
            'message' => 'Banner successfully updated',
            'fields' => $value
        ], 201);
    }


    public function add_marketplace(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|int',
        ]);
        if ($validator->fails()) {
            $validators = $validator->errors()->toArray();
            $data = [
                'validations' => $validators,
                'message' => $validator->errors()->first()
            ];
            return response()->json($data, 400);
        }
        $new_array = array();
        $value = PagesField::find($request['id']);
        $value = json_decode($value['value'], true);
        foreach ($value['items'] as $key) {
            //print_r($key);
            //  $array = array(
            //     'heading'=> $key['heading'],
            //     'text'=> $key['text'],
            //     'image'=> $key['image']
            // );
            //array_push($new_array, $array);
        }
        //  $value = json_encode($value);
        //  $fields = PagesField::where('id', '=', $request->id)
        //  ->update(['value'=>$value]);

        return response()->json([
            'message' => 'Banner successfully updated',
            'fields' => $value
        ], 201);
    }

    public function delete_banner(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|int',
            'index' => 'required|int'
        ]);
        if ($validator->fails()) {
            $validators = $validator->errors()->toArray();
            $data = [
                'validations' => $validators,
                'message' => $validator->errors()->first()
            ];
            return response()->json($data, 400);
        }
        $new_array = array();
        $value = PagesField::find($request['id']);
        $value = json_decode($value['value'], true);
        unset($value[$request->index]);
        foreach ($value as $key) {
            $array = array(
                'heading' => $key['heading'],
                'text' => $key['text'],
                'image' => $key['image']
            );
            array_push($new_array, $array);
        }
        $new_array = json_encode($new_array);
        $fields = PagesField::where('id', '=', $request->id)
            ->update(['value' => $new_array]);

        return response()->json([
            'message' => 'Banner successfully updated',
            'fields' => $new_array
        ], 201);
    }

    public function uploadfile_to_s3($base64, $file_name, $path)
    {

        $result = false;
        if ($base64) {
            //$base64 = $request->file;
            $imageData = str_replace(' ', '+', $base64);
            list($type, $imageData) = explode(';', $imageData);
            list(, $extension) = explode('/', $type);
            list(, $imageData) = explode(',', $imageData);
            if ($extension == 'svg+xml') {
                $extension = 'svg';
            }
            $name = $file_name . "." . $extension;
            $imageData = base64_decode($imageData);

            if (Storage::disk('s3')->put($path . '/' . $name, $imageData, 'public')) {
                $result = $this->global['aws_s3_base'] . $path . '/' . $name . '?' . uniqid();
            }

        }
        return $result;
    }


}
