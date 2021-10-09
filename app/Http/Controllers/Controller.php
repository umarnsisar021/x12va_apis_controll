<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Storage;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $global;

    public function __construct()
    {
        $this->global = config('app.global');
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
                $result = $this->global['aws_s3_base'] . $path . '/' . $name;
            }

        }
        return $result;
    }

}
