<?php
namespace App\Http\Controllers\Test;

use App\Models\Test\Test_attempts;
use App\Models\Test\Test_attempts_answers;
use App\Models\Test\Test_questions;
use App\Models\Test\Test_questions_options;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Test\Tests;
use App\Models\Members\Members;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Validator;

class TestAttemptsController extends Controller
{

    public function get_data(Request $request)
    {
        $perPage = request('perPage', 10);
        $search = request('q');
        $test_questions = Test_attempts::orderByDesc('test_attempts.created_at')
            ->leftjoin('tests', 'tests.id', '=', 'test_attempts.test_id')
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

        $record = Tests::find($request['id']);
        $record->delete();
        $test_questions = Test_questions::where('test_id', $request['id'])->get();
        foreach ($test_questions as $test_question) {
            Test_questions_options::where('question_id', $test_question->id)->delete();
        }
        Test_questions::where('test_id', $request['id'])->delete();

        return response()->json([
            'message' => 'Record successfully deleted'
        ], 201);
    }

    public function update(Request $request)
    {
        $rules = Tests::rules($request['id']);
        $rules['id'] = ['required', 'exists:tests,id'];


        $validator = Validator::make($request->all(), $rules);


        $validator = $validator->validated();
        $id = $validator['id'];
        unset($validator['id']);


        DB::beginTransaction();
        $test = Tests::where('id', $id)
            ->update($validator);


        $options = [];
        $option_ids = [];
        if (isset($request->options) && is_array($request->options)) {
            $options = $request->options;
        }
        if (isset($request->option_ids) && is_array($request->option_ids)) {
            $option_ids = $request->option_ids;
        }


        $question_ids = [];
        if (isset($request->question_ids) && is_array($request->question_ids)) {
            $question_ids = $request->question_ids;
        }

        if (isset($request->questions) && is_array($request->questions)) {
            foreach ($request->questions as $index => $question) {
                if (empty($question)) {
                    continue;
                }
                $question_data = array(
                    'test_id' => $request->id,
                    'skill_id' => $request->skill_id,
                    'question' => $question
                );
                if (isset($question_ids[$index])) {
                    $question = Test_questions::where('id', $question_ids[$index])
                        ->update($question_data);
                    $question_id = $question_ids[$index];

                } else {
                    $question_id = Test_questions::create($question_data);
                }


                foreach ($options[$index] as $option_index => $option) {
                    if (empty($option)) {
                        continue;
                    }
                    $option_data = array(
                        'question_id' => $question_id,
                        'text' => $option
                    );

                    if (isset($option_ids[$index][$option_index])) {
                        $question_option = Test_questions_options::where('id', $option_ids[$index][$option_index])
                            ->update($option_data);

                    } else {
                        $question_option = Test_questions_options::create($option_data);
                    }
                }

            }
        }


        if (isset($request->question_deletes) && is_array($request->question_deletes)) {
            foreach ($request->question_deletes as $question_delete) {
                Test_questions_options::where('question_id', $question_delete)->delete();
                Test_questions::where('id', $question_delete)->delete();
            }
        }

        if (isset($request->option_deletes) && is_array($request->option_deletes)) {
            foreach ($request->option_deletes as $option_delete) {
                Test_questions_options::where('id', $option_delete)->delete();
            }
        }

        if (!$test) {
            DB::rollBack();
            return response()->json([
                'message' => 'info missing'
            ], 400);
        }

        DB::commit();

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

        $test = Tests::find($request['id']);
        $test_questions = Test_questions::where('test_id', $request['id'])->get();
        foreach ($test_questions as $index => $test_question) {
            $test_questions_options = Test_questions_options::where('question_id', $test_question->id)
                ->select('id', 'text as option')
                ->get();
            $test_questions[$index]['options'] = $test_questions_options;
        }
        return response()->json([
            'message' => 'Get Record successfully',
            'record' => $test,
            'test_questions' => $test_questions
        ], 201);
    }



    public function api_start_test(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'test_id' => 'required'
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
        $test_attempt = Test_attempts::create(
            array(
                'test_id' => $request->test_id,
                'member_id' => $member_record->id,
                'start_time' => Date('Y-m-d H:i')
            )
        );

        if ($test_attempt) {

            return response()->json([
                'message' => 'Test Start',
                'test_attempt_id' => $test_attempt->id,
                'status' => 201
            ], 201);
        } else {
            return response()->json([
                'message' => 'Test  not Start',
                'status' => 400
            ], 400);
        }


    }

    public function api_end_test(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'test_attempt_id' => 'required'
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

        $test_attempt_record = Test_attempts::where(['id' => $request->test_attempt_id, 'member_id' => $member_record->id])->first();
        if (!$test_attempt_record || empty($request->test_attempt_id)) {
            return response()->json([
                'message' => 'invalid id'
            ], 400);
        }
        DB::beginTransaction();
        $test_attempt_record->update(['status'=>1]);
        $test_result = Test_attempts::where('id', $test_attempt_record->id)
            ->update(array(
                'end_time' => Date('Y-m-d H:i'
                )));


        if ($test_result) {
            if (isset($request->answers) && is_array($request->answers)) {
                foreach ($request->answers as $answer) {
                    $answer_array = array(
                        'attempt_id' => $test_attempt_record->id,
                        'question_id' => $answer['question_id'],
                        'option_id' => $answer['option_id'],
                    );
                     Test_attempts_answers::create($answer_array);
                }
            }

            DB::commit();
            return response()->json([
                'message' => 'Test Successfully Done',
                'status' => 201
            ], 201);
        } else {
            DB::rollBack();
            return response()->json([
                'message' => 'Test not End',
                'status' => 400
            ], 400);
        }


    }


}
