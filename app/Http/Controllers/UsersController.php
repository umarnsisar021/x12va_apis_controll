<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Validator;


class UsersController extends Controller
{

    protected $global;

    public function __construct()
    {
        $this->global = config('app.global');
    }

    public function get_data(Request $request)
    {
        $perPage = request('perPage', 10);
        $search = request('q');
        $role = request('role');

        $user = User::orderByDesc('id');
        if (!empty($search)) {
            $user->where('name', 'like', '%' . $search . '%');
        }

        if (!empty($role)) {
            $user->where('role', $role);
        }

        $users = $user->paginate($perPage);
        return response()->json($users);
    }


    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
            'role' => 'required|string|between:2,100',
        ]);

        if ($validator->fails()) {
            $validators = $validator->errors()->toArray();
            $data = [
                'validations' => $validators,
                'message' => $validator->errors()->first()
            ];
            return response()->json($data, 400);
        }


        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password),]
        ));

        $avatar_name = 'user-' . $user->id;
        $avatar = '';
        $request->avatar;
        if ($request->avatar) {
            $avatar = $this->uploadfile_to_s3($request->avatar, $avatar_name, 'avatars');
            User::where('id', $user->id)->update(['avatar' => $avatar]);
        }


        return response()->json([
            'message' => 'User successfully created',
            'user' => $user
        ], 201);
    }

    public function update(Request $request)
    {
        $rules = User::rules($request['id']);
        $rules['id'] = ['required', 'exists:users,id'];
        if (empty($request['password'])) {
            unset($request['password']);
            unset($rules['password']);
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $validators = $validator->errors()->toArray();
            $data = [
                'validations' => $validators,
                'message' => $validator->errors()->first()
            ];
            return response()->json($data, 400);
        }

        $avatar_name = 'user-' . $request['id'];
        $avatar = '';
        $request->avatar;
        if ($request->new_avatar) {
            $avatar = $this->uploadfile_to_s3($request->avatar, $avatar_name, 'avatars');
        }


        $validator = $validator->validated();
        $id = $validator['id'];

        unset($validator['id']);
        unset($validator['new_avatar']);
        if ($request->new_avatar) {
            $validator['avatar'] = $avatar;
        }
        User::where('id', $id)
            ->update($validator);
        return response()->json([
            'message' => 'User successfully updated',
            'user' => $validator
        ], 201);
    }


    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            $validators = $validator->errors()->toArray();
            $data = [
                'validations' => $validators,
                'message' => $validator->errors()->first()
            ];
            return response()->json($data, 400);
        }

        $user = User::find($request['id']);
        $user->delete();
        return response()->json([
            'message' => 'User successfully deleted'
        ], 201);
    }


    public function get(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            $validators = $validator->errors()->toArray();
            $data = [
                'validations' => $validators,
                'message' => $validator->errors()->first()
            ];
            return response()->json($data, 400);
        }

        $user = User::find($request['id']);
        return response()->json([
            'message' => 'Get User successfully',
            'user' => $user
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
                $result = $this->global['aws_s3_base'] . $path . '/' . $name;
            }

        }
        return $result;
    }
}
