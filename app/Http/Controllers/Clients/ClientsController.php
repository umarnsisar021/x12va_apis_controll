<?php
namespace App\Http\Controllers\Clients;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Clients\Clients;
use App\Models\Members\Members;
use App\Models\Experts\Experts;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Validator;

class ClientsController extends Controller
{


    public function get_data(Request $request)
    {
        $perPage = request('perPage', 10);
        $search = request('q');
        $clients = Clients::orderByDesc('id');
        if (!empty($search)) {
            $clients->where('first_name', 'like', '%' . $search . '%')->orWhere('last_name', 'like', '%' . $search . '%');
        }

        if (!empty($role)) {
            //$user->where('role', $role);
        }

        $clients = $clients->paginate($perPage);
        return response()->json($clients);
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

        $chck = ExpertsSkills::where('expert_id', '=', $request['expert_id'])
            ->where('skill_id', '=', $request['skill_id'])->get();

        if (count($chck) > 0) {
            return response()->json([
                'message' => 'Selected skill already exsist.',
            ], 202);
        } else {
            $ExpertsSkills = ExpertsSkills::create($validator->validated());
            $skills = ExpertsSkills::where('expert_id', '=', $request['expert_id'])
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
            'expert' => $validator
        ], 201);
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
        $skills = ExpertsSkills::where('expert_id', '=', $expert['id'])
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





    public function api_login(Request $request)
    {


        if ($request->type == 0) {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string|min:6',
            ]);
        } elseif ($request->type == 1) {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string',
                'first_name' => 'required|string|between:2,100',
                'last_name' => 'required|string|between:2,100',
                'avatar' => 'required|string',
                'google_id' => 'required|string'
            ]);
        } elseif ($request->type == 2) {
            $validator = Validator::make($request->all(), [
                'email' => 'string',
                'first_name' => 'required|string|between:2,100',
                'last_name' => 'required|string|between:2,100',
                'avatar' => 'required|string',
                'facebook_id' => 'required|string'
            ]);
        }

        if ($validator->fails()) {
            $validators = $validator->errors()->toArray();
            $data = [
                'validations' => $validators,
                'message' => $validator->errors()->first()
            ];
            return response()->json($data, 400);
        }

        if ($request->type == 0) {
            $member_record = Members::where('email', '=', $request->email)->get();
            if (count($member_record) > 0) {
                $member_record = $member_record[0];
                if (!Hash::check($request->password, $member_record->password)) {
                    return response()->json(['success' => false, 'message' => 'Login Fail, pls check password']);
                }

                $user_record=array();
                if($member_record->is_seller==1){
                    $user_record=  Experts::where('member_id', '=', $member_record->id)->get();
                }
                if($member_record->is_seller==1){
                    $user_record=  Clients::where('member_id', '=', $member_record->id)->get();
                }

                $token = bin2hex(random_bytes(64));
                $user_info=array(
                    'first_name'=>$user_record->first_name,
                    'last_name'=>$user_record->last_name,
                    'email'=>$user_record->email,
                    'is_seller'=>$member_record->is_seller,
                    'is_buyer'=>$member_record->is_buyer,
                    'mobile_number'=>$user_record->mobile_number,
                    'username'=>$member_record->username
                );
                Members::where('id', $member_record->id)
                    ->update(['token' => $token]);
                return response()->json([
                    'message' => 'Account successfully login',
                    'token' => $token,
                    'user_info'=>$user_info
                ], 201);
            } else {
                return response()->json([
                    'message' => 'wrong username or password'
                ], 400);
            }
        } elseif ($request->type == 1) {
            $member_record = Members::where('google_id', '=', $request->google_id)->get();
            if(count($member_record)>0){
                $token = bin2hex(random_bytes(64));

                $user_record=array();
                if($member_record->is_seller==1){
                    $user_record=  Experts::where('member_id', '=', $member_record->id)->get();
                }
                if($member_record->is_seller==1){
                    $user_record=  Clients::where('member_id', '=', $member_record->id)->get();
                }

                $user_info=array(
                    'id'=>$member_record->id,
                    'first_name'=>$user_record->first_name,
                    'last_name'=>$user_record->last_name,
                    'email'=>$user_record->email,
                    'is_seller'=>$member_record->is_seller,
                    'is_buyer'=>$member_record->is_buyer,
                    'mobile_number'=>$user_record->mobile_number,
                    'username'=>$member_record->username
                );
                Members::where('id', $member_record->id)
                    ->update(['token' => $token]);
                return response()->json([
                    'message' => 'Account successfully login',
                    'token' => $token,
                    'user_info'=>$user_info
                ], 201);
            }else{
                DB::beginTransaction();
                $member = Members::create(array_merge(
                    $validator->validated(),
                    [
                        'email' => $request->email,
                        'is_buyer' => 1,
                        'google_id' => $request->google_id,
                        'signup_with'=>1
                    ]
                ));
                $b64image ="data:image/jpg;base64,". base64_encode(file_get_contents($request->avatar));
//                $file_name=$member->id.uniqid('img_', true);
                $avatar_name = 'user-' . $member->id;


                $avatar=$this->uploadfile_to_s3($b64image,$avatar_name,'avatars');
                $clients = Clients::create(array_merge(
                    $validator->validated(),
                    [
                        'member_id' => $member->id,
                        'first_name' => $request->first_name,
                        'last_name' => $request->last_name,
                        'email' => $request->email,
                        'avatar'=>$avatar
                    ]
                ));
                if (!$clients) {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'info missing'
                    ], 400);
                }
                DB::commit();

                $token = bin2hex(random_bytes(64));
                $user_info=array(
                    'id'=>$member->id,
                    'first_name'=>$clients->first_name,
                    'last_name'=>$clients->last_name,
                    'email'=>$clients->email,
                    'is_seller'=>$member->is_seller,
                    'is_buyer'=>$member->is_buyer,
                    'mobile_number'=>'',
                    'username'=>''
                );
                Members::where('id', $member->id)
                    ->update(['token' => $token]);
                return response()->json([
                    'message' => 'Account successfully login',
                    'token' => $token,
                    'user_info'=>$user_info
                ], 201);
            }
        }

        DB::beginTransaction();
        $member = Members::create(array_merge(
            $validator->validated(),
            [
                'username' => $request->username,
                'email' => $request->email,
                'is_buyer' => 1,
                'password' => bcrypt($request->password)
            ]
        ));


        $clients = Clients::create(array_merge(
            $validator->validated(),
            [
                'member_id' => $member->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'd_o_b' => $request->d_o_b,
                'gender' => $request->gender,
                'email' => $request->email
            ]
        ));
        if (!$clients) {
            DB::rollBack();
            return response()->json([
                'message' => 'info missing'
            ], 400);
        }
        DB::commit();
        return response()->json([
            'message' => 'Account successfully created'
        ]);
    }

    public function api_register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|between:2,100|unique:members',
            'password' => 'required|string|max:100',
            'email' => 'required|string|unique:members',
            'first_name' => 'required|string|between:2,100',
            'last_name' => 'required|string|between:2,100',
            'd_o_b' => 'required|string',
            'gender' => 'required|string'
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
            [
                'username' => $request->username,
                'email' => $request->email,
                'is_buyer' => 1,
                'password' => bcrypt($request->password)
            ]
        ));


        $clients = Clients::create(array_merge(
            $validator->validated(),
            [
                'member_id' => $member->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'd_o_b' => $request->d_o_b,
                'gender' => $request->gender,
                'email' => $request->email
            ]
        ));
        if (!$clients) {
            DB::rollBack();
            return response()->json([
                'message' => 'info missing'
            ], 400);
        }
        DB::commit();
        return response()->json([
            'message' => 'Account successfully created'


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

}
