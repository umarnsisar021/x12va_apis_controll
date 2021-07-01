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
use Validator;

class TasksController extends Controller
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
        $clients = Tasks::orderByDesc('id')
            ->leftjoin('skills', 'skills.id', '=', 'tasks.skill_id')
            ->leftjoin('clients', 'clients.member_id', '=', 'tasks.client_id')
            ->leftjoin('experts', 'experts.member_id', '=', 'tasks.expert_id')
            ->select('tasks.*', 'skills.name as skill_name',
                'clients.first_name as client_first_name',
                'clients.last_name as client_last_name',
                'experts.first_name as expert_first_name',
                'experts.last_name as expert_last_name');
        if (!empty($search)) {
            $clients->where('clients.first_name', 'like', '%' . $search . '%')->orWhere('clients.last_name', 'like', '%' . $search . '%');
        }

        if (!empty($role)) {
            //$user->where('role', $role);
        }

        $clients = $clients->paginate($perPage);
        return response()->json($clients);
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

        $user = Tasks::find($request['id']);
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
        if ($validator->fails()) {
            $validators = $validator->errors()->toArray();
            $data = [
                'validations' => $validators,
                'message' => $validator->errors()->first()
            ];
            return response()->json($data, 400);
        }

        $task = Tasks::find($request->id);
        $client = Clients::where('member_id',$task->client_id)->select('clients.*','members.username')->leftjoin('members', 'members.id', '=', 'clients.member_id')->first();
        $expert = Experts::where('member_id',$task->expert_id)->select('experts.*','members.username')->leftjoin('members', 'members.id', '=', 'experts.member_id')->first();
        return response()->json([
            'message' => 'Get Record successfully',
            'client' => $client,
            'expert' => $expert,
            'record' => $task
        ], 201);
    }

    public function api_add_task(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'skill_id' => 'required',
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
