<?php
namespace App\Http\Controllers\Test;

use App\Models\Test\Test_questions;
use App\Models\Test\Test_questions_options;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Test\Tests;
use App\Models\Members\Members;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Validator;

class TestTemplatesController extends Controller
{

    public function get_data(Request $request)
    {
        $perPage = request('perPage', 10);
        $search = request('q');
        $test_questions = Tests::orderByDesc('tests.id')
            ->leftjoin('skills', 'skills.id', '=', 'tests.skill_id')
            ->select('tests.*', 'skills.name as skill_name');
        if (!empty($search)) {
            $test_questions->where('tests.name', 'like', '%' . $search . '%');
        }

        if (!empty($role)) {
            //$user->where('role', $role);
        }

        $test_questions = $test_questions->paginate($perPage);
        return response()->json($test_questions);
    }

    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'skill_id' => 'required',
            'name' => 'required|string',
            'duration' => 'required',
            'passing_percentage' => 'required|numeric|min:1|max:100'
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


        $test = Tests::create($validator->validated());


        $options = [];
        if (isset($request->options) && is_array($request->options)) {
            $options = $request->options;
        }


        if (isset($request->questions) && is_array($request->questions)) {
            foreach ($request->questions as $index => $question) {
                $question_data = array(
                    'test_id' => $test->id,
                    'skill_id' => $test->skill_id,
                    'question' => $question
                );
                $question = Test_questions::create($question_data);

                foreach ($options[$index] as $option) {
                    $option_data = array(
                        'question_id' => $question->id,
                        'text' => $option
                    );
                    $question_option = Test_questions_options::create($option_data);
                }

            }
        }


//        if(isset($request->options)){
//            $request->options;
//        }


        if (!$test) {
            DB::rollBack();
            return response()->json([
                'message' => 'info missing'
            ], 400);
        }

        DB::commit();
        return response()->json([
            'message' => 'Data successfully added',
            'record' => $test
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

        $user = Clients::find($request['id']);
        $user->delete();
        return response()->json([
            'message' => 'Record successfully deleted'
        ], 201);
    }

    public function update(Request $request)
    {
        $rules = Clients::rules($request['id']);
        $rules['id'] = ['required', 'exists:clients,id'];
        $validator = Validator::make($request->all(), [
            'skill_id' => 'required',
            'name' => 'required|string',
            'duration' => 'required',
            'passing_percentage' => 'required|numeric|min:1|max:100'
        ]);

        $validator = Validator::make($request->all(), $rules);


        $validator = $validator->validated();
        $id = $validator['id'];
        unset($validator['id']);
        Clients::where('id', $id)
            ->update($validator);
        return response()->json([
            'message' => 'Record successfully updated',
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

        $client = Clients::find($request['id']);
        $member = Members::find($client['member_id']);

        return response()->json([
            'message' => 'Get Record successfully',
            'member' => $member,
            'record' => $client
        ], 201);
    }


    public function api_get_test_detail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'skill_id' => 'required',
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
        $test_record = Tests::where('skill_id', $request->skill_id)
            ->select('id','name','description','duration','passing_percentage')->first();

        if ($test_record) {
            $test_questions = Test_questions::where('test_id', $test_record->id)
                ->select('id','question')->get();
            foreach ($test_questions as $index => $test_question) {
                $test_questions_options = Test_questions_options::where('question_id', $test_question->id)
                    ->select('id','text as option')
                    ->get();
                $test_questions[$index]['options'] = $test_questions_options;
            }
        }

        return response()->json([
            'message' =>  ' Record Found ',
            'record' => $test_record,
            'questions'=>$test_questions
        ], 201);
    }


}
