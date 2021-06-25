<?php
namespace App\Http\Controllers\Pages;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Pages\Pages;
use App\Models\Pages\PagesField;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Validator;

class PagesController extends Controller
{
    protected  $global;
    public function __construct(){
        $this->global =  config('app.global');
    }


    public function get_fields(Request $request) {
        $validator = Validator::make($request->all(),[
            'id' => 'required|int'
        ]);


        if($validator->fails()){
            $validators=$validator->errors()->toArray();
            $data=[
                'validations'=>$validators,
                'message'=>$validator->errors()->first()
            ];
            return response()->json($data, 400);
        }

        $fields = PagesField::join('pages', 'pages_field.page_id', '=', 'pages.id')
        ->select('pages.name','pages.id as p_id', 'pages_field.*')
        ->get();
        return response()->json([
            'message' => 'successfull',
            'fields' => $fields
        ], 201);
    }

    public function uploadfile_to_s3($base64,$file_name,$path)
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

            if (Storage::disk('s3')->put($path.'/'.$name,$imageData,'public')) {
                $result = $this->global['aws_s3_base'].$path.'/'.$name;
            }

       }
       return $result;
    }


}
