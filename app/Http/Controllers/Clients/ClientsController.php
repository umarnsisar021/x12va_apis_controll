<?php
namespace App\Http\Controllers\Clients;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Experts\Experts;
use App\Models\Experts\ExpertsEducation;
use App\Models\Experts\ExpertsSkills;
use App\Models\Experts\ExpertsTools;
use App\Models\Members\Members;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Validator;

class ClientsController extends Controller
{
    protected  $global;
    public function __construct(){
        $this->global =  config('app.global');
    }
     public function get_data(Request $request)
    {
        $perPage = request('perPage', 10);
        $search = request('q');
        $experts = Experts::orderByDesc('id');
        if (!empty($search)) {
            $experts->where('first_name', 'like', '%' . $search . '%')->orWhere('last_name', 'like', '%' . $search . '%');
        }

        if (!empty($role)) {
            //$user->where('role', $role);
        }

        $experts = $experts->paginate($perPage);
        return response()->json($experts);
    }

    public function register_new_client(Request $request) {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|between:2,100|unique:members',
            'password' => 'required|string|max:100',
            'email' => 'required|string|unique:members',
            'first_name' => 'required|string|between:2,100',
            'middle_name' => 'string|between:2,100',
            'last_name' => 'required|string|between:2,100',
            'd_o_b' => 'required|string',
            'gender' => 'required|string',
            'country' => 'required|string',
            'mobile_number' => 'required|string',
        ]);

        if($validator->fails()){
            $validators=$validator->errors()->toArray();
            $data=[
                'validations'=>$validators,
                'message'=>$validator->errors()->first()
            ];
            return response()->json($data, 400);
        }

        // $member = Members::create(array_merge(
        //     $validator->validated(),
        //     ['password' => bcrypt($request->password)]
        // ));


        // $avatar_name = $request->username.'-'.$member->id;
        // $avatar = '';
        // $request->avatar;

        // if ($request->avatar) {
        //    $avatar = $this->uploadfile_to_s3($request->avatar,$avatar_name,'avatars');
        // }
        // $experts = Experts::create(array_merge(
        //     $validator->validated(),
        //     [
        //         'member_id'=>  $member->id,
        //         'avatar' => $avatar,
        //     ]

        // ));
        return response()->json([
            'message' => 'Expert`s Education successfully created',
            'ExpertsEducation' => ''
        ], 201);
    }

    public function addExpertEducation(Request $request) {
        $validator = Validator::make($request->all(), [
            'expert_id' => 'required|string',
            'institute_name' => 'required|string',
            'degree' => 'required|string',
            'date_from' => 'required|string',
            'date_to' => 'required|string',

        ]);


        if($validator->fails()){
            $validators=$validator->errors()->toArray();
            $data=[
                'validations'=>$validators,
                'message'=>$validator->errors()->first()
            ];
            return response()->json($data, 400);
        }

        $ExpertsEducation = ExpertsEducation::create($validator->validated());
        return response()->json([
            'message' => 'Expert`s Education successfully created',
            'ExpertsEducation' => $ExpertsEducation
        ], 201);
    }


    public function addExpertSkills(Request $request) {
        $validator = Validator::make($request->all(), [
            'expert_id' => 'int|string',
            'skill_id' => 'int',
        ]);


        if($validator->fails()){
            $validators=$validator->errors()->toArray();
            $data=[
                'validations'=>$validators,
                'message'=>$validator->errors()->first()
            ];
            return response()->json($data, 400);
        }

        $chck = ExpertsSkills::where('expert_id','=', $request['expert_id'])
        ->where('skill_id','=', $request['skill_id'])->get();

        if (count($chck) > 0) {
            return response()->json([
                'message' => 'Selected skill already exsist.',
            ], 202);
        }
        else{
             $ExpertsSkills = ExpertsSkills::create($validator->validated());
             $skills = ExpertsSkills::where('expert_id','=', $request['expert_id'])
            ->join('skills', 'experts_skills.skill_id', '=', 'skills.id')
            ->select('experts_skills.*', 'skills.name', 'skills.short_code')
            ->get();
                return response()->json([
                    'message' => 'Expert`s Skill successfully created',
                    'skills' => $skills
                ], 201);
        }


    }

    public function addExpertTool(Request $request) {
        $validator = Validator::make($request->all(), [
            'expert_id' => 'required|string',
            'name' => 'required|string|between:2,100',
        ]);


        if($validator->fails()){
            $validators=$validator->errors()->toArray();
            $data=[
                'validations'=>$validators,
                'message'=>$validator->errors()->first()
            ];
            return response()->json($data, 400);
        }

        $chck = ExpertsTools::where('expert_id','=', $request['expert_id'])
        ->where('name','=', $request['name'])->get();

        if (count($chck) > 0) {
            return response()->json([
                'message' => 'Already exsist.',
            ], 202);
        }
        else{
             $ExpertsTools = ExpertsTools::create($validator->validated());
             $tool = ExpertsTools::where('expert_id','=', $request['expert_id'])
            ->get();
                return response()->json([
                    'message' => 'Expert`s Skill successfully created',
                    'skills' => $tool
                ], 201);
        }


    }

    public function update(Request $request)
    {
        $rules = Experts::rules($request['id']);
        $rules['id'] = ['required', 'exists:experts,id'];
        if (empty($request['password'])) {
            unset($request['password']);
            unset($rules['password']);
        }
           if (empty($request['status'])) {
            unset($request['status']);
            unset($rules['status']);
        }

        $validator = Validator::make($request->all(), $rules);


        $validator= $validator->validated();
        $id = $validator['id'];
        unset($validator['id']);
        Experts::where('id', $id)
            ->update($validator);
        return response()->json([
            'message' => 'Data successfully updated',
            'expert' => $validator
        ], 201);
    }



     public function updateEducation(Request $request)
    {
        $rules = ExpertsEducation::rules($request['id']);
        $rules['id'] = ['required', 'exists:experts_education,id'];

        $validator = Validator::make($request->all(), $rules);
        $validator= $validator->validated();
        $id = $validator['id'];
        unset($validator['id']);
        ExpertsEducation::where('id', $id)
            ->update($validator);
        return response()->json([
            'message' => 'Data successfully updated',
            'ExpertsEducation' => $validator
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

        $user = Experts::find($request['id']);
        $user->delete();
        return response()->json([
            'message' => 'Expert successfully deleted'
        ], 201);
    }
    public function deleteExpertEducation(Request $request)
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

        $ExpertsEducation = ExpertsEducation::find($request['id']);
        $ExpertsEducation->delete();
        return response()->json([
            'message' => 'Expert Education successfully deleted'
        ], 201);
    }


    public function deleteExpertSkill(Request $request)
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

        $ExpertsSkills = ExpertsSkills::find($request['id']);
        $ExpertsSkills->delete();
        return response()->json([
            'message' => 'Skill successfully deleted'
        ], 201);
    }

     public function deleteExpertTool(Request $request)
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

        $ExpertsSkills = ExpertsTools::find($request['id']);
        $ExpertsSkills->delete();
        return response()->json([
            'message' => 'Tool successfully deleted'
        ], 201);
    }


     public function changeExpertPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'new_password' => 'required',
        ]);
        if($validator->fails()){
            $validators=$validator->errors()->toArray();
            $data=[
                'validations'=>$validators,
                'message'=>$validator->errors()->first()
            ];
            return response()->json($data, 400);
        }
        $id = $request->id;
        Members::where('id', $id)
            ->update(['password' => bcrypt($request->new_password)]);
        return response()->json([
            'message' => 'Password successfully updated.'
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

        $expert = Experts::find($request['id']);
        $member = Members::find($expert['member_id']);
        $education = ExpertsEducation::where('expert_id','=', $expert['id'])->get();
        $skills = ExpertsSkills::where('expert_id','=', $expert['id'])
        ->join('skills', 'experts_skills.skill_id', '=', 'skills.id')
        ->select('experts_skills.*', 'skills.name', 'skills.short_code')
        ->get();
        $tools = ExpertsTools::where('expert_id','=', $expert['id'])->get();
        return response()->json([
            'message' => 'Get Expert successfully',
            'member' => $member,
            'expert' => $expert,
            'education' => $education,
            'skills'=>$skills,
            'tools'=> $tools
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
