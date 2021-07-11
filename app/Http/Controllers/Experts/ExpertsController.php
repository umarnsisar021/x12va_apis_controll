<?php

namespace App\Http\Controllers\Experts;

use App\Models\App\Skills;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Experts\Experts;
use App\Models\Experts\ExpertsEducation;
use App\Models\Experts\ExpertsSkills;
use App\Models\Experts\ExpertsTools;
use App\Models\Members\Members;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Validator;

class ExpertsController extends Controller
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
        $status = isset($request->status) ? $request->status : 0;
        $experts = Experts::orderByDesc('id');
        if (!empty($search)) {
            $experts->where('first_name', 'like', '%' . $search . '%')->orWhere('last_name', 'like', '%' . $search . '%');
        }
        if (!empty($status) || $status == 0) {
            $experts->where('status', $status);
        }

        $experts = $experts->paginate($perPage);
        return response()->json($experts);
    }

    public function add(Request $request)
    {
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


        if ($validator->fails()) {
            $validators = $validator->errors()->toArray();
            $data = [
                'validations' => $validators,
                'message' => $validator->errors()->first()
            ];
            return response()->json($data, 400);
        }
        DB::beginTransaction();

        $member = Members::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password), 'is_seller' => 1]
        ));


        $avatar_name = $request->username . '-' . $member->id;
        $avatar = '';


        if ($request->avatar) {
            $avatar = $this->uploadfile_to_s3($request->avatar, $avatar_name, 'avatars');
        }
        $experts = Experts::create(array_merge(
            $validator->validated(),
            [
                'member_id' => $member->id,
                'avatar' => $avatar,
            ]
        ));

        if (!$experts) {
            DB::rollBack();
            return response()->json([
                'message' => 'info missing'
            ], 400);
        }

        DB::commit();
        return response()->json([
            'message' => 'Data successfully added',
            'record' => $experts
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

        $user = Experts::find($request['id']);
        $user->delete();
        return response()->json([
            'message' => 'Expert successfully deleted'
        ], 201);
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


        $validator = $validator->validated();
        $id = $validator['id'];
        unset($validator['id']);
        Experts::where('id', $id)
            ->update($validator);
        return response()->json([
            'message' => 'Data successfully updated',
            'record' => $validator
        ], 201);
    }


    public function status_change(Request $request)
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


        Experts::where('id', $request->id)
            ->update(['status' => $request->status]);
        return response()->json([
            'message' => 'Data successfully updated',
            'record' => $validator
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

        $expert = Experts::find($request['id']);
        $member = Members::find($expert['member_id']);
        $education = ExpertsEducation::where('expert_id', '=', $expert['id'])->get();
        $skills = ExpertsSkills::where('member_id', '=', $member['id'])
            ->join('skills', 'experts_skills.skill_id', '=', 'skills.id')
            ->select('experts_skills.*', 'skills.name', 'skills.short_code')
            ->get();
        $tools = ExpertsTools::where('expert_id', '=', $expert['id'])->get();
        return response()->json([
            'message' => 'Get Expert successfully',
            'member' => $member,
            'expert' => $expert,
            'education' => $education,
            'skills' => $skills,
            'tools' => $tools
        ], 201);
    }


    public function addExpertEducation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'expert_id' => 'required|string',
            'institute_name' => 'required|string',
            'degree' => 'required|string',
            'date_from' => 'required|string',
            'date_to' => 'required|string',

        ]);


        if ($validator->fails()) {
            $validators = $validator->errors()->toArray();
            $data = [
                'validations' => $validators,
                'message' => $validator->errors()->first()
            ];
            return response()->json($data, 400);
        }

        $ExpertsEducation = ExpertsEducation::create($validator->validated());
        return response()->json([
            'message' => 'Expert`s Education successfully created',
            'ExpertsEducation' => $ExpertsEducation
        ], 201);
    }


    public function addExpertSkills(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'expert_id' => 'int|string',
            'skill_id' => 'int',
        ]);


        if ($validator->fails()) {
            $validators = $validator->errors()->toArray();
            $data = [
                'validations' => $validators,
                'message' => $validator->errors()->first()
            ];
            return response()->json($data, 400);
        }

        $expert = Experts::find($request['id']);

        $chck = ExpertsSkills::where('member_id', '=', $expert['member_id'])
            ->where('skill_id', '=', $request['skill_id'])->get();

        if (count($chck) > 0) {
            return response()->json([
                'message' => 'Selected skill already exsist.',
            ], 202);
        } else {
            $expertsSkills = ExpertsSkills::create($validator->validated());
            $skills = ExpertsSkills::where('member_id', '=', $expert['member_id'])
                ->join('skills', 'experts_skills.skill_id', '=', 'skills.id')
                ->select('experts_skills.*', 'skills.name', 'skills.short_code')
                ->get();
            return response()->json([
                'message' => 'Expert`s Skill successfully created',
                'skills' => $skills
            ], 201);
        }


    }

    public function addExpertTool(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'expert_id' => 'required|string',
            'name' => 'required|string|between:2,100',
        ]);


        if ($validator->fails()) {
            $validators = $validator->errors()->toArray();
            $data = [
                'validations' => $validators,
                'message' => $validator->errors()->first()
            ];
            return response()->json($data, 400);
        }

        $chck = ExpertsTools::where('expert_id', '=', $request['expert_id'])
            ->where('name', '=', $request['name'])->get();

        if (count($chck) > 0) {
            return response()->json([
                'message' => 'Already exsist.',
            ], 202);
        } else {
            $ExpertsTools = ExpertsTools::create($validator->validated());
            $tool = ExpertsTools::where('expert_id', '=', $request['expert_id'])
                ->get();
            return response()->json([
                'message' => 'Expert`s Skill successfully created',
                'skills' => $tool
            ], 201);
        }


    }


    public function updateEducation(Request $request)
    {
        $rules = ExpertsEducation::rules($request['id']);
        $rules['id'] = ['required', 'exists:experts_education,id'];

        $validator = Validator::make($request->all(), $rules);
        $validator = $validator->validated();
        $id = $validator['id'];
        unset($validator['id']);
        ExpertsEducation::where('id', $id)
            ->update($validator);
        return response()->json([
            'message' => 'Data successfully updated',
            'ExpertsEducation' => $validator
        ], 201);
    }


    public function deleteExpertEducation(Request $request)
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
        if ($validator->fails()) {
            $validators = $validator->errors()->toArray();
            $data = [
                'validations' => $validators,
                'message' => $validator->errors()->first()
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
        if ($validator->fails()) {
            $validators = $validator->errors()->toArray();
            $data = [
                'validations' => $validators,
                'message' => $validator->errors()->first()
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
        if ($validator->fails()) {
            $validators = $validator->errors()->toArray();
            $data = [
                'validations' => $validators,
                'message' => $validator->errors()->first()
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


    public function api_find_total_experts(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'skill_id' => 'required'
        ]);

        if ($validator->fails()) {
            $validators = $validator->errors()->toArray();
            $data = [
                'validations' => $validators,
                'message' => $validator->errors()->first()
            ];
            return response()->json($data, 400);
        }

        $records = Experts::where('experts_skills.skill_id', '=', $request->skill_id)
            ->select('experts.id')
            ->leftjoin('experts_skills', 'experts_skills.member_id', '=', 'experts.member_id')->get()->count();

        return response()->json([
            'records' => $records
        ], 201);
    }


    public function api_get_skills_with_experts()
    {
        $skills = Skills::where('status', 0)->select('id', 'name', 'short_code')->get();
        foreach ($skills as $index => $skill) {
            $skills[$index]['experts'] = Experts::where('experts.status', 0)
                ->select('first_name', 'last_name', 'experts.id', 'avatar', 'members.username')
                ->leftjoin('members', 'members.id', '=', 'experts.member_id')
                ->leftjoin('experts_skills', 'experts_skills.member_id', '=', 'members.id')
                ->where('experts_skills.skill_id', $skill->id)->limit(10)->get();
        }
        return response()->json([
            'records' => $skills
        ], 201);
    }


    public function api_get_experts_public_profile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required'
        ]);

        if ($validator->fails()) {
            $validators = $validator->errors()->toArray();
            $data = [
                'validations' => $validators,
                'message' => $validator->errors()->first()
            ];
            return response()->json($data, 400);
        }
        $expert = Experts::where('members.username', $request->username)
            ->select('experts.*', 'members.username')
            ->leftjoin('members', 'members.id', '=', 'experts.member_id')
            ->first();
        if (!$expert) {
            return response()->json([
                'message' => 'Expert Not Found'
            ], 400);
        }
        $skills = Skills::where('skills.status', 0)
            ->select('skills.id', 'skills.name', 'skills.short_code')
            ->leftjoin('experts_skills', 'experts_skills.skill_id', '=', 'skills.id')
            ->where('experts_skills.member_id', $expert->member_id)->get();
        return response()->json([
            'expert_detail' => $expert,
            'skills' => $skills
        ], 201);
    }


    public function api_register_as_a_reference_code(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'd_o_b' => 'required',
            'gender' => 'required',
            'email' => 'required',
            'reference_code' => 'required',

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
                'message' => 'invalid token'
            ], 400);
        }
        $expert_record = Experts::where('member_id', '=', $member_record->id)->first();
        if ($expert_record) {
            return response()->json([
                'message' => 'Already Applied',
                'status'=>400
            ], 200);
        }

        $expert_record = Experts::where('reference_code', '=', $request->reference_code)->first();
        if (!$expert_record || empty($request->reference_code)) {
            return response()->json([
                'message' => 'invalid Reference Code'
            ], 400);
        }

        DB::beginTransaction();
        $member = Members::where('id', $member_record->id)
            ->update(['is_seller' => 1]);

        if ($member) {

            $expert = Experts::create(array_merge(
                $validator->validated(),
                [
                    'member_id' => $member_record->id,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'd_o_b' => $request->d_o_b,
                    'gender' => $request->gender,
                    'email' => $request->email
                ]
            ));


            $user_info=array(
                'id'=>$member_record->id,
                'first_name'=>$expert->first_name,
                'last_name'=>$expert->last_name,
                'email'=>$expert->email,
                'is_seller'=>1,
                'is_buyer'=>$member_record->is_buyer,
                'mobile_number'=>$expert->mobile_number,
                'username'=>$member_record->username,
                'avatar' => $expert->avatar,
            );

            if ($expert) {
                DB::commit();

                return response()->json([
                    'message' => 'Expert successfully Registered',
                    'token' => $token,
                    'user_info'=>$user_info
                ], 201);
            } else {
                DB::rollBack();
                return response()->json([
                    'message' => 'some thing wrong'
                ], 400);
            }
        } else {
            DB::rollBack();
            return response()->json([
                'message' => 'some thing wrong'
            ], 400);
        }


    }



    public function api_skill_add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'skill_id' => 'required'
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
                'message' => 'invalid token'
            ], 400);
        }

        $has_skill = ExpertsSkills::where(['skill_id' => $request->skill_id, 'member_id' => $member_record->id])->first();
        if ($has_skill) {
            return response()->json([
                'message' => 'already have is skill',
                'status' => 400
            ], 400);
        }
        DB::beginTransaction();
        $experts_skills=ExpertsSkills::create([
            'skill_id'=>$request->skill_id,
            'member_id' => $member_record->id
        ]);

        if ($experts_skills) {

            DB::commit();
            return response()->json([
                'message' => 'Successfully Added',
                'status' => 201
            ], 201);
        } else {
            DB::rollBack();
            return response()->json([
                'message' => 'Some thing wrong',
                'status' => 400
            ], 400);
        }


    }

    public function api_get_my_skills(Request $request)
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
                'message' => 'invalid token'
            ], 400);
        }

        $skills = ExpertsSkills::where([ 'member_id' => $member_record->id])
            ->select('skills.id','skills.name','experts_skills.status')
            ->leftjoin('skills', 'skills.id', '=', 'experts_skills.skill_id')
            ->get();


        return response()->json([
            'message' => 'Successfully Added',
            'records'=>$skills
        ], 201);


    }


    public function api_change_profile(Request $request)
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
                'message' => 'invalid token'
            ], 400);
        }

        $expert = Experts::where(['member_id' => $member_record->id])->first();
        if (!$expert) {
            return response()->json([
                'message' => 'Expert not found',
                'status' => 400
            ], 400);
        }

        $avatar_name = $member_record->username . '-' . $member_record->id;
        $avatar = '';
        if ($request->avatar) {
            $avatar = $this->uploadfile_to_s3($request->avatar, $avatar_name, 'avatars');
        }else{
            return response()->json([
                'message' => 'Avatar not found',
                'status' => 400
            ], 400);
        }

        DB::beginTransaction();
       $expert_result=     Experts::where('id', $expert->id)
                ->update(['avatar'=>$avatar]);
        if ($expert_result) {

            DB::commit();
            return response()->json([
                'message' => 'Profile Successfully Update',
                'status' => 201
            ], 201);
        } else {
            DB::rollBack();
            return response()->json([
                'message' => 'Some thing wrong',
                'status' => 400
            ], 400);
        }


    }

}
