<?php

namespace App\Http\Controllers\App;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\App\Skills;
use Illuminate\Support\Facades\Hash;
use Validator;


class SkillsController extends Controller
{


    public function get_data(Request $request)
    {
        $perPage = request('perPage', 10);
        $search = request('q');
        $skill = Skills::orderByDesc('id');
        if (!empty($search)) {
            $user->where('name', 'like', '%' . $search . '%');
        }

        if (!empty($role)) {
            //$user->where('role', $role);
        }

        $skills = $skill->paginate($perPage);
//         $userData =$user->map(function($user){
//             return [
//                 'id' => $user->id,
//                 'first_name' => $user->first_name,
//                 'last_name' => $user->last_name,
//                 'email' => $user->email,
//                 'mobile_no' => $user->mobile_no,
//                 'address' => $user->address,
//                 'edit_url' => route('users.edit',$user->id),
//                 'delete_url' => route('users.destroy',$user->id),
//             ];
//         });
        return response()->json($skills);
    }

    public function get_all(Request $request)
    {
        $validator = Validator::make($request->all(), []);
        if($validator->fails()){
            $validators=$validator->errors()->toArray();
            $data=[
                'validations'=>$validators,
                'message'=>$validator->errors()->first()
            ];
            return response()->json($data, 400);
        }
       $skills = Skills::get();
        return response()->json([
            'message' => 'Skill successfully',
            'skills' => $skills
        ], 201);
    }

     public function get_select_list(Request $request)
    {
        $validator = Validator::make($request->all(), []);
        if($validator->fails()){
            $validators=$validator->errors()->toArray();
            $data=[
                'validations'=>$validators,
                'message'=>$validator->errors()->first()
            ];
            return response()->json($data, 400);
        }
       $skills = Skills::get();
        return response()->json([
            'message' => 'Skill successfully created',
            'skills' => $skills
        ], 201);
    }

    public function add(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100|unique:countries',
            'short_code' => 'required|string|max:100',
        ]);

        if($validator->fails()){
            $validators=$validator->errors()->toArray();
            $data=[
                'validations'=>$validators,
                'message'=>$validator->errors()->first()
            ];
            return response()->json($data, 400);
        }

        $skills = Skills::create(array_merge(
            $validator->validated()
        ));

        return response()->json([
            'message' => 'Skill successfully created',
            'skills' => $skills
        ], 201);
    }

    public function update(Request $request)
    {
        $rules = Countries::rules($request['id']);
        $rules['id'] = ['required', 'exists:countries,id'];
        if (empty($request['password'])) {
            unset($request['password']);
            unset($rules['password']);
        }
           if (empty($request['status'])) {
            unset($request['status']);
            unset($rules['status']);
        }

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            $validators=$validator->errors()->toArray();
            $data=[
                'validations'=>$validators,
                'message'=>$validator->errors()->first()
            ];
            return response()->json($data, 400);
        }
        $validator= $validator->validated();
        $id = $validator['id'];
        unset($validator['id']);
        Countries::where('id', $id)
            ->update($validator);
        return response()->json([
            'message' => 'Country successfully updated',
            'country' => $validator
        ], 201);
    }


    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if($validator->fails()){
            $validators=$validator->errors()->toArray();
            $data=[
                'validations'=>$validators,
                'message'=>$validator->errors()->first()
            ];
            return response()->json($data, 400);
        }

        $user = Countries::find($request['id']);
        $user->delete();
        return response()->json([
            'message' => 'Country successfully deleted'
        ], 201);
    }


    public function get(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if($validator->fails()){
            $validators=$validator->errors()->toArray();
            $data=[
                'validations'=>$validators,
                'message'=>$validator->errors()->first()
            ];
            return response()->json($data, 400);
        }

        $skill = Skills::find($request['id']);
        return response()->json([
            'message' => 'Get Skill successfully',
            'skill' => $skill
        ], 201);
    }


    public function api_list(){
        $skills=Skills::all('id','name');
        return response()->json([
            'records' => $skills
        ], 201);
    }
}
