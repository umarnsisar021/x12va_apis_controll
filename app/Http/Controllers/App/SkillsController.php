<?php

namespace App\Http\Controllers\App;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\App\Skills;
use Illuminate\Support\Facades\Hash;
use Validator;


class SkillsController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->middleware('can:apps/skills-view')->only(['get_data','get']);
        $this->middleware('can:apps/skills-add')->only(['add']);
        $this->middleware('can:apps/skills-edit')->only(['update']);
        $this->middleware('can:apps/skills-delete')->only(['delete']);
    }

    public function get_data(Request $request)
    {
        $perPage = request('perPage', 10);
        $search = request('q');
        $records = Skills::orderByDesc('id');
        if (!empty($search)) {
            $records->where('name', 'like', '%' . $search . '%');
        }
        $records = $records->paginate($perPage);
        return response()->json($records);
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
            'name' => 'required|string|between:2,100|unique:skills',
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
        $rules = Skills::rules($request['id']);
        $rules['id'] = ['required', 'exists:skills,id'];
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
        Skills::where('id', $id)
            ->update($validator);
        return response()->json([
            'message' => 'Record successfully updated'
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

        $user = Skills::find($request['id']);
        $user->delete();
        return response()->json([
            'message' => 'Record successfully deleted'
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
