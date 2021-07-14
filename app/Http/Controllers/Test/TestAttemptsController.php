<?php
namespace App\Http\Controllers\Test;

use App\Models\Experts\ExpertsSkills;
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
        $test_attempts = Test_attempts::orderByDesc('test_attempts.created_at')
            ->leftjoin('tests', 'tests.id', '=', 'test_attempts.test_id')
            ->leftjoin('skills', 'skills.id', '=', 'tests.skill_id')
            ->leftjoin('experts', 'experts.member_id', '=', 'test_attempts.member_id')
            ->select('test_attempts.id', 'tests.name as test_name',
                'tests.duration as test_duration',
                'experts.first_name',
                'experts.last_name',
                'experts.avatar',
                'skills.name as skill_name',
                'test_attempts.start_time',
                'test_attempts.end_time',
                'test_attempts.status',
                DB::raw('TIMESTAMPDIFF(MINUTE,tbl_test_attempts.start_time,tbl_test_attempts.end_time) as minutes'));
        if (!empty($search)) {
            $test_attempts->where('tests.name', 'like', '%' . $search . '%');
        }

        if (!empty($role)) {
            //$user->where('role', $role);
        }

        $test_attempts = $test_attempts->paginate($perPage);
        return response()->json($test_attempts);
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

        $record = Test_attempts::find($request['id']);
        Test_attempts_answers::where('attempt_id',$request['id'])->delete();
        $record->delete();

        return response()->json([
            'message' => 'Record successfully deleted'
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

        $test_attempt = Test_attempts::orderByDesc('test_attempts.created_at')
            ->leftjoin('tests', 'tests.id', '=', 'test_attempts.test_id')
            ->leftjoin('skills', 'skills.id', '=', 'tests.skill_id')
            ->leftjoin('experts', 'experts.member_id', '=', 'test_attempts.member_id')
            ->leftjoin('members', 'members.id', '=', 'test_attempts.member_id')

            ->select(
                'test_attempts.id',
                'tests.id as test_id',
                'tests.name as test_name',
                'tests.duration as test_duration',
                'experts.first_name',
                'experts.last_name',
                'experts.mobile_number',
                'members.email',
                'members.username',
                'experts.avatar',
                'skills.name as skill_name',
                'test_attempts.start_time',
                'test_attempts.end_time',
                'test_attempts.status',
                DB::raw('TIMESTAMPDIFF(MINUTE,tbl_test_attempts.start_time,tbl_test_attempts.end_time) as minutes'))
            ->where('test_attempts.id',$request['id'])->first();


        $test_questions = Test_questions::where('test_id', $test_attempt->test_id)->get();
        foreach ($test_questions as $index => $test_question) {
            $test_questions_options = Test_questions_options::where('question_id', $test_question->id)
                ->select('id', 'text as option')
                ->get();
            $test_questions[$index]['options'] = $test_questions_options;
        }



        return response()->json([
            'message' => 'Get Record successfully',
            'record' => $test_attempt,
            'questions'=>$test_questions
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

        $test_attempt = Test_attempts::find($request->id);

        if (!$test_attempt) {
            return response()->json([
                'message' => 'some thing wrong'
            ], 401);
        }

        DB::beginTransaction();
        $result = Test_attempts::where('id', $request->id)
            ->update(['status' => $request->status]);

        if ($result) {
            if ($request->status == 2) {
                $test = Tests::find($test_attempt['test_id']);
                $result = ExpertsSkills::where(['member_id' => $test_attempt['member_id'], 'skill_id' => $test['skill_id']])
                    ->update(['status' => 1]);
            }
            DB::commit();
            return response()->json([
                'message' => 'Data successfully updated',
                'record' => $validator
            ], 201);
        } else {
            DB::rollBack();
            return response()->json([
                'message' => 'some thing wrong'
            ], 400);
        }

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
        $test_attempt_record->update(['status' => 1]);
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
