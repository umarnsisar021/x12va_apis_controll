<?php
namespace App\Http\Controllers\Accounts;

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

class TransactionsController extends Controller
{
    protected $global;

    public function __construct()
    {
        $this->global = config('app.global');
    }


    public function api_add_payment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'card_holder_name' => 'required',
            'card_no' => 'required',
            'expiration_date' => 'required',
            'cw_code' => 'required',
            'proposal_id'=>'required'
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
                'message' => 'You have not permission '
            ], 404);
        }



        DB::beginTransaction();
        $transaction = Transactions::create(
            [
                'primary_id' => $task->id,
                'primary_table' =>(new Tasks)->getTable(),
                'type' =>0,
                'status' => 1,
                'debit' => $tasks_proposal->budget,
                'member_id' => $member_record->id
            ]
        );

        if ($transaction) {
            DB::commit();
            return response()->json([
                'message' => 'Payment successfully added your wallet '
            ], 201);
        } else {
            DB::rollBack();
            return response()->json([
                'message' => 'some thing wrong'
            ], 400);
        }


    }


}
