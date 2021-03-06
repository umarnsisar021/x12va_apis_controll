<?php
namespace App\Http\Controllers\Tasks;

use App\Models\Accounts\Transactions;
use App\Models\Experts\Experts;
use App\Models\Settings\System_settings;
use App\Models\Tasks\Tasks_proposals;
use App\Models\Tasks\Tasks;
use App\Models\Tasks\Tasks_rating;
use App\Models\Tasks\Tasks_remainders;
use App\Models\Tasks\Tasks_status_histories;
use App\Models\Tasks\Tasks_transaction_release;
use App\Models\Tasks\Tasks_updates;
use App\Models\Tasks\Tasks_updates_comments;
use DateTime;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Clients\Clients;
use App\Models\Members\Members;
use App\Models\Notifications;

use Illuminate\Support\Facades\DB;
use Validator;

class TasksController extends Controller
{


    public function __construct()
    {
        parent::__construct();
        $this->middleware('can:tasks/tasks-view')->only(['get_data', 'get']);
        $this->middleware('can:tasks/tasks-add')->only(['add']);
        $this->middleware('can:tasks/tasks-edit')->only(['update']);
        $this->middleware('can:tasks/tasks-delete')->only(['delete']);
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
                'message' => 'invalid token', 'status' => 405
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
                $notification = Notifications::create(
                    [
                        'primary_id' => $task->id,
                        'title' => "New Task Request Received",
                        'message' => $request->description,
                        'type' => 1,
                        'member_id' => $expert_record->member_id,
                        'from_member_id' => $member_record->id

                    ]
                );
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
                'message' => 'invalid token', 'status' => 405
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

        if ($tasks_proposal->total_payable > $member_record->balance) {
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


            $notification = Notifications::create(
                [
                    'primary_id' => $task->id,
                    'title' => "Task Assigned",
                    'message' => 'Task #' . $task->id . ' Assigned You',
                    'type' => 3,
                    'member_id' => $tasks_proposal->member_id,
                    'from_member_id' => $member_record->id

                ]
            );

            $transaction = Transactions::create(
                [
                    'primary_id' => $task->id,
                    'primary_table' => (new Tasks)->getTable(),
                    'type' => 1,
                    'status' => 1,
                    'credit' => $tasks_proposal->total_payable,
                    'member_id' => $member_record->id,
                    'description' => 'Assign Task, Task #' . $task->id,
                    'trans_purpose' => 1
                ]
            );


            if ($task->days >= 3) {
                $days_remainders = round($task->days / 3);
                $Date1 = date('Y-m-d H:i');
                $date = new DateTime($Date1);
                $date->modify('+' . $days_remainders . ' day');
                for ($i = 0; $i < $days_remainders; $i++) {
                    $date = new DateTime($Date1);
                    $date->modify('+' . $days_remainders . ' day');
                    $Date2 = $date->format('Y-m-d H:i');
                    $Date1 = $Date2;
                    Tasks_remainders::create([
                        'task_id' => $task->id,
                        'date' => $Date2
                    ]);
                }
            }

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
                'message' => 'invalid token', 'status' => 405
            ], 400);
        }

        $records = Tasks::where(['tasks.expert_id' => $member_record->id, 'tasks.status' => $request->status])
            ->select('tasks.id', 'tasks.description as task_description', 'tasks.days', 'skills.name as skill_name', 'task_assign.created_at as assign_date', 'task_complete.created_at as complete_date', 'tasks_proposals.budget','tasks_proposals.total_payable')
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


    public function api_get_expert_tasks_send_proposals(Request $request)
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
                'message' => 'invalid token', 'status' => 405
            ], 400);
        }

        $records = Tasks::where(['tasks_proposals.member_id' => $member_record->id])
            ->select('tasks.id', 'tasks.description as task_description', 'tasks.days', 'skills.name as skill_name', 'task_assign.created_at as assign_date', 'task_complete.created_at as complete_date', 'tasks_proposals.budget','tasks_proposals.total_payable', 'tasks_proposals.problem_statement', 'tasks_proposals.id as proposal_id')
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
                'message' => 'invalid token', 'status' => 405
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

         $perPage = request('perPage', 10);
         $search = request('q');

        $token = $request->token;
        $member_record = Members::where('token', '=', $token)->first();
        if (!$member_record || empty($token)) {
            return response()->json([
                'message' => 'invalid token', 'status' => 405
            ], 400);
        }

        $records = Tasks::where(['tasks.client_id' => $member_record->id, 'tasks.status' => $request->status])
            ->select('tasks.id', 'tasks.description as task_description', 'tasks.days', 'skills.name as skill_name', 'task_assign.created_at as assign_date', 'task_complete.created_at as complete_date', 'tasks_proposals.budget','tasks_proposals.total_payable')
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
         $records = $records->paginate($perPage);
//        $records = $records->get();

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
                'message' => 'invalid token', 'status' => 405
            ], 400);
        }
        $records = Tasks_proposals::where('tasks.client_id', $member_record->id)
            ->select('tasks_proposals.id', 'tasks_proposals.task_id', 'tasks_proposals.member_id as expert_id', 'tasks_proposals.problem_statement', 'tasks_proposals.description', 'tasks_proposals.budget','tasks_proposals.total_payable', 'tasks.days', 'skills.name as skill_name'
                , 'experts.first_name as expert_first_name', 'experts.last_name as expert_last_name', 'experts.avatar as expert_avatar')
            ->leftjoin('tasks', 'tasks.id', '=', 'tasks_proposals.task_id')
            ->leftjoin('experts', 'experts.member_id', '=', 'tasks_proposals.member_id')
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
                'message' => 'invalid token', 'status' => 405
            ], 400);
        }
        $record = Tasks::where(['tasks.id' => $request->task_id])
            ->select('tasks.*', 'tasks.description as task_description', 'tasks.days', 'skills.name as skill_name', 'task_assign.created_at as assign_date', 'task_complete.created_at as complete_date', 'tasks_proposals.id as proposal_id')
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
                'message' => 'invalid token', 'status' => 405
            ], 400);
        }
        $record = Tasks_proposals::where('tasks.client_id', $member_record->id)
            ->select('tasks_proposals.id', 'tasks_proposals.task_id', 'tasks_proposals.subject', 'tasks_proposals.problem_statement', 'tasks_proposals.description', 'tasks_proposals.budget','tasks_proposals.total_payable', 'tasks.days', 'skills.name as skill_name',
                'tasks_proposals.member_id as expert_id', 'experts.first_name as expert_first_name', 'experts.last_name as expert_last_name', 'experts.avatar as expert_avatar')
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
                'message' => 'invalid token', 'status' => 405
            ], 400);
        }
        $record = Tasks_proposals::select('tasks_proposals.id', 'tasks_proposals.task_id', 'tasks_proposals.subject', 'tasks_proposals.problem_statement', 'tasks_proposals.description', 'tasks_proposals.budget','tasks_proposals.total_payable', 'tasks.days', 'skills.name as skill_name',
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
                'message' => 'invalid token', 'status' => 405,
                'status' => 405
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

        $budget=($request->budget)?$request->budget:0;
        $task_commission_from_expert = System_settings::getSystemSetting('task_commission_from_expert');
        $verifier_commission_from_expert = System_settings::getSystemSetting('verifier_commission_from_expert');
        $task_commission_from_client = System_settings::getSystemSetting('task_commission_from_client');
        $verifier_commission_from_client = System_settings::getSystemSetting('verifier_commission_from_client');
        if (true) {//percentage of budget amount
            $task_commission_from_expert = $task_commission_from_expert * $budget / 100;
            $verifier_commission_from_expert = $verifier_commission_from_expert * $budget / 100;

            $task_commission_from_client = $task_commission_from_client * $budget / 100;
            $verifier_commission_from_client = $verifier_commission_from_client * $budget / 100;
        }
        $total_payable = $budget +($task_commission_from_client+$verifier_commission_from_client);

        DB::beginTransaction();
        $tasks_proposal = Tasks_proposals::create(array_merge(
            $validator->validated(),
            [
                'member_id' => $member_record->id,
                'task_id' => $request->task_id,
                'subject' => $request->subject,
                'problem_statement' => $request->problem_statement,
                'budget' => $budget,
                'task_commission_from_expert' => $task_commission_from_expert,
                'verifier_commission_from_expert' => $verifier_commission_from_expert,
                'task_commission_from_client' => $task_commission_from_client,
                'verifier_commission_from_client' => $verifier_commission_from_client,
                'total_payable' => $total_payable,
                'description' => isset($request->description) ? $request->description : ''
            ]
        ));

        if ($tasks_proposal) {
            $notification = Notifications::create(
                [
                    'primary_id' => $tasks_proposal->id,
                    'title' => "Proposal Received From " . $member_record->first_name . " " . $member_record->last_name,
                    'message' => $tasks_proposal->problem_statement,
                    'type' => 2,
                    'member_id' => $task->client_id,
                    'from_member_id' => $member_record->id,
                ]
            );

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


    public function api_update_task(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'task_id' => 'required',
            'note' => 'required'
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
                'message' => 'invalid token',
                'status' => 405,
                'status' => 405
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

        if ($task->expert_id != $member_record->id) {
            return response()->json([
                'message' => 'You have not permission to update this task'
            ], 404);
        }


        $document = '';
        if (isset($request->document) && !empty($request->document)) {
            $file_name = $member_record->id . uniqid('', true);
            $document = $this->uploadfile_to_s3($request->document, $file_name, 'documents');
        }

        DB::beginTransaction();
        $tasks_update = Tasks_updates::create(
            [
                'member_id' => $member_record->id,
                'task_id' => $request->task_id,
                'note' => $request->note,
                'document' => $document
            ]
        );

        if ($tasks_update) {
            $notification = Notifications::create(
                [
                    'primary_id' => $tasks_update->id,
                    'title' => "Task Update  Task #" . $request->task_id,
                    'message' => $tasks_update->note,
                    'type' => 4,
                    'member_id' => $task->client_id,
                    'from_member_id' => $member_record->id,
                ]
            );

            DB::commit();
            return response()->json([
                'message' => 'successfully Update',
                'update' => $tasks_update->id
            ], 201);

        } else {
            DB::rollBack();
            return response()->json([
                'message' => 'some thing wrong'
            ], 400);
        }


    }


    public function api_get_task_detail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'task_id' => 'required'
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
//        if (!$member_record || empty($token)) {
//            return response()->json([
//                'message' => 'invalid token',
//                'status' => 405,
//            ], 400);
//        }
        $task = Tasks::where('tasks.id', '=', $request->task_id)
            ->select('tasks.*', 'skills.name as skill_name')
            ->leftjoin('skills', 'skills.id', '=', 'tasks.skill_id')->first();

        if (!$task) {
            return response()->json([
                'message' => 'Task not found'
            ], 400);
        }

        if ($task->expert_id != $member_record->id && $task->client_id != $member_record->id) {
            return response()->json([
                'message' => 'You have not permission to view this task'
            ], 404);
        }

        $task_status = config('common_list.task_status');
        $task_detail = [
            'task_id' => $task->id,
            'days' => $task->days,
            'status' => $task->status,
            'description' => $task->description,
            'document' => $task->document,
            'skill_name' => $task->skill_name,
            'created_at' => $task->created_at
        ];

        $expert_detail = [
            'member_id' => $task->experts->member_id,
            'name' => $task->experts->first_name . ' ' . $task->experts->last_name,
            'email' => $task->experts->email,
            'mobile_number' => $task->experts->mobile_number,
            'avatar' => $task->experts->avatar
        ];


        $client_detail = [
            'member_id' => $task->clients->member_id,
            'name' => $task->clients->first_name . ' ' . $task->clients->last_name,
            'email' => $task->clients->email,
            'mobile_number' => $task->clients->mobile_number,
            'avatar' => $task->clients->avatar
        ];


        $accepted_proposal = Tasks_proposals::where(['task_id' => $task->id, 'member_id' => $task->expert_id])
            ->select('subject', 'problem_statement', 'budget','total_payable', 'description', 'created_at')->first();

        $ratings = Tasks_rating::where(['task_id' => $task->id])
            ->select('tasks_ratings.*', 'from_client.first_name as from_name', 'to_client.first_name as to_name')
            ->leftjoin('clients as from_client', 'from_client.member_id', '=', 'tasks_ratings.from_member_id')
            ->leftjoin('clients as to_client', 'to_client.member_id', '=', 'tasks_ratings.to_member_id')->get();


        $task_accept_history=Tasks_status_histories::where(['status'=>1,'task_id'=>$task->id])->select('created_at')->first();

        if($task_accept_history){
            $task_detail['start_time']=$task_accept_history->created_at;
        }


        $task_updates = Tasks_updates::where(['task_id' => $task->id])
            ->select('id', 'note', 'document', 'created_at')
            ->with('comments:id,comment,member_id,update_id')
//            ->with(['comments' => function($query) {
//                $query->select(['id', 'comment','member_id']);
//            }])
            ->get();
        return response()->json([
            'message' => 'successfully Update',
            'task_detail' => $task_detail,
            'expert' => $expert_detail,
            'client' => $client_detail,
            'proposal' => $accepted_proposal,
            'updates' => $task_updates,
            'ratings'=>$ratings
        ], 201);


    }


    public function api_complete_task(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'task_id' => 'required'
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
        $member_record = Members::where('token', '=', $token)->select('members.*', 'clients.first_name', 'clients.last_name')
            ->leftjoin('clients', 'clients.member_id', '=', 'members.id')->first();
        if (!$member_record || empty($token)) {
            return response()->json([
                'message' => 'invalid token',
                'status' => 405,
            ], 400);
        }


        $task = Tasks::where('id', '=', $request->task_id)->select('*')->first();
        if (!$task) {
            return response()->json([
                'message' => 'Task not found'
            ], 404);
        }
        if ($task->client_id != $member_record->id) {
            return response()->json([
                'message' => 'You have not permission to complete this task'
            ], 404);
        }

        if ($task->status == 4) {
            return response()->json([
                'message' => 'Task Already Completed'
            ], 403);
        }

        DB::beginTransaction();
        $tasks_update = Tasks::where('id', $task->id)
            ->update(
                [
                    'status' => 4
                ]
            );

        if ($tasks_update) {

            Tasks_status_histories::create([
                'task_id' => $task->id,
                'status' => 4,
                'member_id' => $member_record->id
            ]);


            $notification = Notifications::create(
                [
                    'primary_id' => $task->id,
                    'title' => "Task Completed",
                    'message' => 'Task #' . $task->id . ' Completed',
                    'type' => 4,
                    'member_id' => $task->expert_id,
                    'from_member_id' => $member_record->id

                ]
            );

            $accepted_proposal = Tasks_proposals::where(['task_id' => $task->id, 'member_id' => $task->expert_id])
                ->select('subject', 'problem_statement', 'budget','total_payable', 'description', 'created_at'
                ,'task_commission_from_expert','verifier_commission_from_expert')->first();

            if (!$accepted_proposal) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Proposal not found'
                ], 400);
            }

            $expert_amount = $accepted_proposal->budget - ($accepted_proposal->task_commission_from_expert + $accepted_proposal->verifier_commission_from_expert);

            $transaction = Transactions::create(
                [
                    'primary_id' => $task->id,
                    'primary_table' => (new Tasks)->getTable(),
                    'type' => 0,
                    'status' => 0,
                    'debit' => $expert_amount,
                    'member_id' => $task->expert_id,
                    'description' => 'Task Completed, Task #' . $task->id,
                    'trans_purpose' => 3
                ]
            );




            $date = date('Y-m-d H:i');
            $date = new DateTime($date);
            $date->modify('+' . 1 . ' day');
            $date=$date->format('Y-m-d H:i');

            $tasks_transaction_release = Tasks_transaction_release::create(
                [
                    'task_id' => $task->id,
                    'date' => $date,
                    'trans_id' => $transaction->id,
                    'status' => 0
                ]
            );


            DB::commit();
            return response()->json([
                'message' => 'Successfully completed'
            ], 201);

        } else {
            DB::rollBack();
            return response()->json([
                'message' => 'some thing wrong'
            ], 400);
        }


    }

    public function api_add_task_rating(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'task_id' => 'required',
            'rate' => 'required',
            'remarks' => 'required',
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
        $member_record = Members::where('token', '=', $token)->select('members.*')->first();
        if (!$member_record || empty($token)) {
            return response()->json([
                'message' => 'invalid token',
                'status' => 405,
            ], 400);
        }


        $task = Tasks::where('id', '=', $request->task_id)->select('*')->first();
        if (!$task) {
            return response()->json([
                'message' => 'Task not found'
            ], 404);
        }
        if ($task->client_id != $member_record->id && $task->expert_id != $member_record->id) {
            return response()->json([
                'message' => 'You have not permission to rate this task'
            ], 404);
        }
        $task_rating = Tasks_rating::where(['task_id' => $task->id, 'from_member_id' => $member_record->id])->select('id')->first();
        if ($task_rating) {
            return response()->json([
                'message' => 'Already rate this task'
            ], 403);
        }

        DB::beginTransaction();

        $to_member_id=0;

        if($task->client_id==$member_record->id){
            $to_member_id=$task->expert_id;
        }elseif($task->expert_id==$member_record->id){
            $to_member_id=$task->client_id;
        }
        $rating = Tasks_rating::create([
            'task_id' => $task->id,
            'from_member_id' => $member_record->id,
            'to_member_id' => $to_member_id,
            'rate' => $request->rate,
            'remarks' => $request->remarks
        ]);

        if ($rating) {
            DB::commit();
            return response()->json([
                'message' => 'Successfully submited'
            ], 201);
        } else {
            DB::rollBack();
            return response()->json([
                'message' => 'some thing wrong'
            ], 400);
        }
    }


    public function api_task_update_comment_add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'update_id' => 'required',
            'comment' => 'required'
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
                'message' => 'invalid token',
                'status' => 405,
            ], 400);
        }

        $tasks_update = Tasks_updates::where('id', $request->update_id)->first();

        if (!$tasks_update) {
            return response()->json([
                'message' => 'Update not found'
            ], 400);
        }

        $task = Tasks::where('tasks.id', '=', $tasks_update->task_id)->first();

        if (!$task) {
            return response()->json([
                'message' => 'Task not found'
            ], 400);
        }

        if ($task->expert_id != $member_record->id && $task->client_id != $member_record->id) {
            return response()->json([
                'message' => 'You have not permission to view this task'
            ], 404);
        }


        DB::beginTransaction();
        $comment = Tasks_updates_comments::create(
            [
                'member_id' => $member_record->id,
                'update_id' => $request->update_id,
                'comment' => $request->comment,
            ]
        );

        if ($comment) {
            $notification = Notifications::create(
                [
                    'primary_id' => $comment->id,
                    'title' =>$member_record->first_name . " " . $member_record->last_name. " Comment Task # ".$task->id ,
                    'message' => $comment->comment,
                    'type' => 9,
                    'member_id' => $task->expert_id,
                    'from_member_id' => $member_record->id,
                ]
            );

            DB::commit();
            return response()->json([
                'message' => 'successfully Added',
                'comment_id' => $comment->id
            ], 201);

        } else {
            DB::rollBack();
            return response()->json([
                'message' => 'some thing wrong'
            ], 400);
        }

    }




}
