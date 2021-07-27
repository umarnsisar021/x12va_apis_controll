<?php
namespace App\Http\Controllers\Tasks;

use App\Models\Accounts\Transactions;
use App\Models\Experts\Experts;
use App\Models\Tasks\Tasks_proposals;
use App\Models\Tasks\Tasks;
use App\Models\Tasks\Tasks_status_histories;
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
        $client = Clients::where('member_id', $task->client_id)->select('clients.*', 'members.username')->leftjoin('members', 'members.id', '=', 'clients.member_id')->first();
        $expert = Experts::where('member_id', $task->expert_id)->select('experts.*', 'members.username')->leftjoin('members', 'members.id', '=', 'experts.member_id')->first();

        if ($client) {
            $client->total_tasks = Tasks::where('client_id', $client->member_id)->get()->count();
            $client->total_completed_tasks = Tasks::where(['client_id' => $client->member_id, 'status' => 3])->get()->count();
        }
        if ($expert) {
            $expert->total_tasks = Tasks::where('expert_id', $expert->member_id)->get()->count();
            $expert->total_completed_tasks = Tasks::where(['expert_id' => $expert->member_id, 'status' => 3])->get()->count();
        }

        $task_timelines = [];
        if ($task) {
            $task_timelines[] = [
                'status' => 0,
                'title' => 'Task create by ' . $client->first_name . ' ' . $client->last_name,
                'description' => substr($task->description, 0, 30),
                'created_at' => $task->created_at->diffForHumans(),
                'name' => $client->first_name . ' ' . $client->last_name,
                'username' => $client->username,
                'avatar' => $client->avatar,
                'role' => 'Client',
                'document' => $task->document,
                'document_extension' => pathinfo($task->document, PATHINFO_EXTENSION)
            ];


            if ($expert) {
                $proposal = Tasks_proposals::where(['member_id' => $task->expert_id, 'task_id' => $task->id])->first();
                $description = '';
                if ($proposal) {
                    $description = $proposal->problem_statement;
                }
                $task_timelines[] = [
                    'status' => 1,
                    'title' => 'Task Assigned ',
                    'description' => substr($description, 0, 30),
                    'created_at' => $proposal->created_at->diffForHumans(),
                    'name' => $expert->first_name . ' ' . $expert->last_name,
                    'avatar' => $expert->avatar,
                    'username' => $expert->username,
                    'role' => 'Expert',

                ];
            }
        }

        return response()->json([
            'message' => 'Get Record successfully',
            'client' => $client,
            'expert' => $expert,
            'record' => $task,
            'task_timelines' => $task_timelines
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

        $document = '';
        if (isset($request->document) && !empty($request->document)) {
            $file_name = $member_record->id . uniqid('', true);
            $document = $this->uploadfile_to_s3($request->document, $file_name, 'documents');
        }
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
                ->leftjoin('experts_skills', 'experts_skills.member_id', '=', 'experts.member_id')->get();

            Tasks_status_histories::create([
                'task_id' => $task->id,
                'status' => 0,
                'member_id' => $member_record->id
            ]);

            foreach ($experts_records as $expert_record) {
                $notification = Notifications::create(array_merge(
                    $validator->validated(),
                    [
                        'primary_id' => $task->id,
                        'title' => "New Task Request Received",
                        'message' => $request->description,
                        'type' => 1,
                        'member_id' => $expert_record->member_id,
                        'from_member_id' => $member_record->id

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


    public function api_assign_task(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'proposal_id' => 'required'
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

        $tasks_proposal = Tasks_proposals::find($request->proposal_id);
        if (!$tasks_proposal) {
            return response()->json([
                'message' => 'Proposal Not Found'
            ], 404);
        }

        $task = Tasks::where('id', $tasks_proposal->task_id)->first();




        if (!$task) {
            return response()->json([
                'message' => 'Task Not Found'
            ], 404);
        }

        if ($task->client_id != $member_record->id) {
            return response()->json([
                'message' => 'You have not permission to assign this task'
            ], 404);
        }

        if ($task->status != 0) {
            return response()->json([
                'message' => 'Task already Assigned'
            ], 404);
        }


        if ($tasks_proposal->budget > $member_record->balance) {
            return response()->json([
                'message' => 'Your Wallet Balance Less then Proposal Budget',
                'status' => 402
            ], 402);
        }


//        Transactions::where(['member_id'=>$member_record->id,'status'=>1]);


        DB::beginTransaction();
        $task_update = Tasks::where('id', $task->id)
            ->update(['status' => 1, 'expert_id' => $tasks_proposal->member_id]);

        if ($task_update) {


            Tasks_status_histories::create([
                'task_id' => $task->id,
                'status' => 1,
                'member_id' => $member_record->id
            ]);


            $notification = Notifications::create(array_merge(
                $validator->validated(),
                [
                    'primary_id' => $task->id,
                    'title' => "Task Assigned",
                    'message' => 'Task #' . $task->id . ' Assigned You',
                    'type' => 3,
                    'member_id' => $tasks_proposal->member_id,
                    'from_member_id' => $member_record->id

                ]
            ));

            $transaction = Transactions::create(
                [
                    'primary_id' => $task->id,
                    'primary_table' =>(new Tasks)->getTable(),
                    'type' =>1,
                    'status' => 1,
                    'credit' => $tasks_proposal->budget,
                    'member_id' => $member_record->id
                ]
            );

            if (!$transaction) {
                DB::rollBack();
                return response()->json([
                    'message' => 'some thing wrong'
                ], 400);
            }
            DB::commit();
            return response()->json([
                'message' => 'Task successfully Assigned'
            ], 201);
        } else {
            DB::rollBack();
            return response()->json([
                'message' => 'some thing wrong'
            ], 400);
        }


    }


    public function api_get_expert_tasks(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'status' => 'required'
        ]);

        if ($validator->fails()) {
            $validators = $validator->errors()->toArray();
            $data = [
                'validations' => $validators,
                'message' => $validator->errors()->first()
            ];
            return response()->json($data, 400);
        }

        // $perPage = request('perPage', 10);
        // $search = request('q');

        $token = $request->token;
        $member_record = Members::where('token', '=', $token)->first();
        if (!$member_record || empty($token)) {
            return response()->json([
                'message' => 'invalid token'
            ], 400);
        }

        $records = Tasks::where(['tasks.expert_id' => $member_record->id, 'tasks.status' => $request->status])
            ->select('tasks.id', 'skills.name as skill_name', 'task_assign.created_at as assign_date', 'task_complete.created_at as complete_date', 'tasks_proposals.budget')
            ->leftjoin('skills', 'skills.id', '=', 'tasks.skill_id')->groupBy('id')
            ->leftJoin('tasks_status_histories as task_assign', function ($join) {
                $join->on('task_assign.task_id', '=', 'tasks.id');
                $join->on('task_assign.status', '=', DB::raw('1'));
            })
            ->leftJoin('tasks_status_histories as task_complete', function ($join) {
                $join->on('task_complete.task_id', '=', 'tasks.id');
                $join->on('task_complete.status', '=', DB::raw('3'));
            })
            ->leftjoin('tasks_proposals', 'tasks_proposals.task_id', '=', 'tasks.id');
        // $records = $records->paginate($perPage);
        $records = $records->get();

        return response()->json([
            'message' => count($records) . ' Orders Found ',
            'records' => $records
        ], 201);


    }


    public function api_get_expert_new_tasks(Request $request)
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

        // $perPage = request('perPage', 10);
        // $search = request('q');

        $token = $request->token;
        $member_record = Members::where('token', '=', $token)->first();
        if (!$member_record || empty($token)) {
            return response()->json([
                'message' => 'invalid token'
            ], 400);
        }

        $records = Tasks::where(['notifications.member_id' => $member_record->id, 'tasks.status' => 0])
            ->select('tasks.*', 'skills.name as skill_name', 'tasks_proposals.id as proposal_id')
            ->leftJoin('notifications', function ($join) {
                $join->on('notifications.primary_id', '=', 'tasks.id');
                $join->on('notifications.type', '=', DB::raw('1'));
            })
            ->leftJoin('tasks_proposals', function ($join) {
                $join->on('tasks_proposals.task_id', '=', 'tasks.id');
                $join->on('tasks_proposals.member_id', '=', 'notifications.member_id');
            })
            ->leftjoin('skills', 'skills.id', '=', 'tasks.skill_id')->groupBy('tasks.id');
        // $records = $records->paginate($perPage);
        $records = $records->get();

        return response()->json([
            'message' => count($records) . ' Orders Found ',
            'records' => $records
        ], 201);


    }


    public function api_get_client_tasks(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'status' => 'required'
        ]);

        if ($validator->fails()) {
            $validators = $validator->errors()->toArray();
            $data = [
                'validations' => $validators,
                'message' => $validator->errors()->first()
            ];
            return response()->json($data, 400);
        }

        // $perPage = request('perPage', 10);
        // $search = request('q');

        $token = $request->token;
        $member_record = Members::where('token', '=', $token)->first();
        if (!$member_record || empty($token)) {
            return response()->json([
                'message' => 'invalid token'
            ], 400);
        }

        $records = Tasks::where(['tasks.client_id' => $member_record->id, 'tasks.status' => $request->status])
            ->select('tasks.id', 'skills.name as skill_name', 'task_assign.created_at as assign_date', 'task_complete.created_at as complete_date', 'tasks_proposals.budget')
            ->leftjoin('skills', 'skills.id', '=', 'tasks.skill_id')->groupBy('id')
            ->leftJoin('tasks_status_histories as task_assign', function ($join) {
                $join->on('task_assign.task_id', '=', 'tasks.id');
                $join->on('task_assign.status', '=', DB::raw('1'));
            })
            ->leftJoin('tasks_status_histories as task_complete', function ($join) {
                $join->on('task_complete.task_id', '=', 'tasks.id');
                $join->on('task_complete.status', '=', DB::raw('3'));
            })
            ->leftjoin('tasks_proposals', 'tasks_proposals.task_id', '=', 'tasks.id');
        // $records = $records->paginate($perPage);
        $records = $records->get();

        return response()->json([
            'message' => count($records) . ' Orders Found ',
            'records' => $records
        ], 201);


    }

    public function api_get_client_proposals(Request $request)
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

        $perPage = request('perPage', 10);
        $search = request('q');

        $token = $request->token;
        $member_record = Members::where('token', '=', $token)->first();
        if (!$member_record || empty($token)) {
            return response()->json([
                'message' => 'invalid token'
            ], 400);
        }
        $records = Tasks_proposals::where('tasks.client_id', $member_record->id)
            ->select('tasks_proposals.id', 'tasks_proposals.task_id', 'tasks_proposals.member_id as expert_id', 'tasks_proposals.problem_statement', 'tasks_proposals.description', 'tasks_proposals.budget', 'tasks.days', 'skills.name as skill_name')
            ->leftjoin('tasks', 'tasks.id', '=', 'tasks_proposals.task_id')
            ->leftjoin('skills', 'skills.id', '=', 'tasks.skill_id');

        if (isset($request->task_id) && !empty($request->task_id)) {
            $records->where('tasks_proposals.task_id', $request->task_id);
        }
        $records = $records->paginate($perPage);
        return response()->json([
            'message' => count($records) . ' Proposals Found ',
            'records' => $records
        ], 201);
    }


    public function api_get_task_by_id(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'task_id' => 'required',
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
        $record = Tasks::where(['tasks.id' => $request->task_id])
            ->select('tasks.*', 'skills.name as skill_name', 'task_assign.created_at as assign_date', 'task_complete.created_at as complete_date', 'tasks_proposals.id as proposal_id')
            ->leftjoin('skills', 'skills.id', '=', 'tasks.skill_id')->groupBy('id')
            ->leftJoin('tasks_status_histories as task_assign', function ($join) {
                $join->on('task_assign.task_id', '=', 'tasks.id');
                $join->on('task_assign.status', '=', DB::raw('1'));
            })
            ->leftJoin('tasks_status_histories as task_complete', function ($join) {
                $join->on('task_complete.task_id', '=', 'tasks.id');
                $join->on('task_complete.status', '=', DB::raw('3'));
            })
            ->leftJoin('tasks_proposals', function ($join) use ($member_record) {
                $join->on('tasks_proposals.task_id', '=', 'tasks.id');
                $join->on('tasks_proposals.member_id', '=', DB::raw($member_record->id));
            });
        // $records = $records->paginate($perPage);
        $record = $record->first();

        return response()->json([
            'message' => ' Task Found ',
            'records' => $record
        ], 201);
    }


    public function api_get_proposal_by_id(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'proposal_id' => 'required',
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
        $record = Tasks_proposals::where('tasks.client_id', $member_record->id)
            ->select('tasks_proposals.id', 'tasks_proposals.task_id', 'tasks_proposals.subject', 'tasks_proposals.problem_statement', 'tasks_proposals.description', 'tasks_proposals.budget', 'tasks.days', 'skills.name as skill_name',
                'tasks_proposals.member_id as expert_id', 'experts.first_name as expert_first_name', 'experts.last_name as expert_last_name')
            ->leftjoin('tasks', 'tasks.id', '=', 'tasks_proposals.task_id')
            ->leftjoin('experts', 'experts.member_id', '=', 'tasks_proposals.member_id')
            ->leftjoin('skills', 'skills.id', '=', 'tasks.skill_id');
        $record->where('tasks_proposals.id', $request->proposal_id);
        $record = $record->first();
        return response()->json([
            'message' => ' Proposal Found ',
            'records' => $record
        ], 201);
    }


    public function api_get_proposal_by_id_expert(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'proposal_id' => 'required',
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
        $record = Tasks_proposals::select('tasks_proposals.id', 'tasks_proposals.task_id', 'tasks_proposals.subject', 'tasks_proposals.problem_statement', 'tasks_proposals.description', 'tasks_proposals.budget', 'tasks.days', 'skills.name as skill_name',
            'tasks_proposals.member_id as expert_id', 'experts.first_name as expert_first_name', 'experts.last_name as expert_last_name')
            ->leftjoin('tasks', 'tasks.id', '=', 'tasks_proposals.task_id')
            ->leftjoin('experts', 'experts.member_id', '=', 'tasks_proposals.member_id')
            ->leftjoin('skills', 'skills.id', '=', 'tasks.skill_id');
        $record->where(['tasks_proposals.id' => $request->proposal_id, 'tasks_proposals.member_id' => $member_record->id]);
        $record = $record->first();
        return response()->json([
            'message' => ' Proposal Found ',
            'records' => $record
        ], 201);
    }


    public function api_send_proposal_task(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'task_id' => 'required',
            'subject' => 'required',
            'problem_statement' => 'required',
            'budget' => 'required'
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
        $member_record = Members::where('token', '=', $token)->select('members.*', 'experts.first_name', 'experts.last_name')
            ->leftjoin('experts', 'experts.member_id', '=', 'members.id')->first();
        if (!$member_record || empty($token)) {
            return response()->json([
                'message' => 'invalid token'
            ], 400);
        }

        if ($member_record->is_seller != 1) {
            return response()->json([
                'message' => 'member not expert'
            ], 400);
        }
        $task = Tasks::where('id', '=', $request->task_id)->first();
        if (!$task) {
            return response()->json([
                'message' => 'Task not found'
            ], 400);
        }

        DB::beginTransaction();
        $tasks_proposal = Tasks_proposals::create(array_merge(
            $validator->validated(),
            [
                'member_id' => $member_record->id,
                'task_id' => $request->task_id,
                'subject' => $request->subject,
                'problem_statement' => $request->problem_statement,
                'budget' => $request->budget,
                'description' => isset($request->description) ? $request->description : ''
            ]
        ));

        if ($tasks_proposal) {
            $notification = Notifications::create(array_merge(
                $validator->validated(),
                [
                    'primary_id' => $task->id,
                    'title' => "Proposal Received From " . $member_record->first_name . " " . $member_record->last_name,
                    'message' => $tasks_proposal->problem_statement,
                    'type' => 2,
                    'member_id' => $task->client_id,
                    'from_member_id' => $member_record->id,
                ]
            ));

            DB::commit();
            return response()->json([
                'message' => 'Proposal successfully Send',
                'proposal_id' => $tasks_proposal->id
            ], 201);

        } else {
            DB::rollBack();
            return response()->json([
                'message' => 'some thing wrong'
            ], 400);
        }


    }

}
