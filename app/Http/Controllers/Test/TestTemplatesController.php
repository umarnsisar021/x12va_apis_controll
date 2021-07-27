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
                if (empty($question)) {
                    continue;
                }
                $type = $request->types[$index]['key'];
                $question_data = array(
                    'test_id' => $test->id,
                    'question' => $question,
                    'type' => $type
                );

                $question = Test_questions::create($question_data);
                if ($type == 1) {
                    foreach ($options[$index] as $option_index => $option) {
                        if (empty($option)) {
                            continue;
                        }
                        $option_data = array(
                            'question_id' => $question->id,
                            'text' => $option
                        );
                        $question_option = Test_questions_options::create($option_data);
                        $correct = $request->corrects[$index];
                        if ($correct == $option_index) {
                            $test_questions = Test_questions::where('id', $question->id)
                                ->update(['correct_option_id' => $question_option->id]);

                        }
                    }
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

                $type = $request->types[$index]['key'];
                $question_data = array(
                    'test_id' => $request->id,
                    'question' => $question,
                    'type' => $type
                );
                if (isset($question_ids[$index])) {
                    $question = Test_questions::where('id', $question_ids[$index])
                        ->update($question_data);
                    $question_id = $question_ids[$index];
                } else {
                    $question_created = Test_questions::create($question_data);
                    $question_id=$question_created->id;
                }

                if ($type == 1) {
                    foreach ($options[$index] as $option_index => $option) {
                        if (empty($option)) {
                            continue;
                        }
                        $option_data = array(
                            'question_id' => $question_id,
                            'text' => $option
                        );


                        $correct_option_id = '';
                        if (isset($option_ids[$index][$option_index])) {
                            $question_option = Test_questions_options::where('id', $option_ids[$index][$option_index])
                                ->update($option_data);
                            $correct_option_id = $option_ids[$index][$option_index];
                        } else {
                            $question_option = Test_questions_options::create($option_data);
                            $correct_option_id = $question_option->id;
                        }

                        $correct = $request->corrects[$index];
                        if ($correct == $option_index) {
                            $test_questions = Test_questions::where('id', $question_id)
                                ->update(['correct_option_id' => $correct_option_id]);

                        }
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
            ->select('id', 'name', 'description', 'duration', 'passing_percentage')->first();

        $test_questions = [];
        if ($test_record) {
            $test_questions = Test_questions::where('test_id', $test_record->id)
                ->select('id', 'question')->get();
            foreach ($test_questions as $index => $test_question) {
                $test_questions_options = Test_questions_options::where('question_id', $test_question->id)
                    ->select('id', 'text as option')
                    ->get();
                $test_questions[$index]['options'] = $test_questions_options;
            }
        }

        return response()->json([
            'message' => ' Record Found ',
            'record' => $test_record,
            'questions' => $test_questions
        ], 201);
    }


}
