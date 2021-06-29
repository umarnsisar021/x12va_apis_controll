<?php
namespace App\Http\Controllers\Tasks;

use App\Models\Experts\Experts;
use App\Models\Tasks\Tasks;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Clients\Clients;
use App\Models\Members\Members;
use App\Models\Notifications;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Validator;

class TasksController extends Controller
{
    protected $global;

    public function __construct()
    {
        $this->global = config('app.global');
    }


    public function api_add_task(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required',
            'days' => 'required'
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
        $file_name = $member_record->id . uniqid('', true);

        $document = $this->uploadfile_to_s3($request->document, $file_name, 'documents');

        DB::beginTransaction();
        $task = Tasks::create(array_merge(
            $validator->validated(),
            [
                'client_id' => $member_record->id,
                'description' => $request->description,
                'days' => $request->days,
                'skill_id' => $request->skill_id,
                'document' => $document
            ]
        ));

        if ($task) {
            $experts_records = Experts::where('experts_skills.skill_id', '=', $request->skill_id)
                ->select('experts.member_id')
                ->leftjoin('experts_skills', 'experts_skills.expert_id', '=', 'experts.id')->get();

            foreach ($experts_records as $expert_record) {
                $notification = Notifications::create(array_merge(
                    $validator->validated(),
                    [
                        'primary_id' => $task->id,
                        'title' => "New Task Request Received",
                        'message' => $request->description,
                        'type' => 1,
                        'member_id' => $expert_record->member_id
                    ]
                ));
            }
            DB::commit();
            return response()->json([
                'message' => 'Task successfully generate',
                'task_id' => $task->id
            ], 201);

        } else {
            DB::rollBack();
            return response()->json([
                'message' => 'some thing wrong'
            ], 400);
        }


    }

}
