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

    public function get_wallet_data(Request $request)
    {
        $perPage = request('perPage', 10);
        $search = request('q');
        $clients = Members::orderByDesc('members.id')
            ->select('members.id', 'clients.first_name', 'clients.last_name', 'clients.avatar')
            ->selectRaw('SUM(tbl_transactions.debit) AS total_debit')
            ->selectRaw('SUM(tbl_transactions.credit) AS total_credit')
            ->selectRaw('SUM(tbl_transactions.debit)-SUM(tbl_transactions.credit) AS balance')
            ->leftjoin('transactions', 'transactions.member_id', '=', 'members.id')
            ->leftjoin('clients', 'clients.member_id', '=', 'members.id')->groupBy('members.id');
        if (!empty($search)) {
            $clients->where('clients.first_name', 'like', '%' . $search . '%')->orWhere('clients.last_name', 'like', '%' . $search . '%');
        }

        $clients = $clients->paginate($perPage);
        return response()->json($clients);
    }


    public function ledger(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'member_id' => 'required',
            'from_date' => 'required',
            'to_date' => 'required'
        ]);


        if ($validator->fails()) {
            $validators = $validator->errors()->toArray();
            $data = [
                'validations' => $validators,
                'message' => $validator->errors()->first()
            ];
            return response()->json($data, 400);
        }


        $transactions = Transactions::where('member_id', $request->member_id)->get();

        return response()->json([
            'message' => 'successfully ',
            'records' => $transactions
        ], 201);


    }


    public function api_add_payment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'card_holder_name' => 'required',
            'card_no' => 'required',
            'expiration_date' => 'required',
            'cw_code' => 'required',
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
                'message' => 'You have not permission '
            ], 404);
        }


        DB::beginTransaction();
        $transaction = Transactions::create(
            [
                'primary_id' => $task->id,
                'primary_table' => (new Tasks)->getTable(),
                'type' => 0,
                'status' => 1,
                'debit' => $tasks_proposal->budget,
                'member_id' => $member_record->id,
                'description' => 'Add Card Payment, Task #' . $task->id,
                'trans_purpose' => 0
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


    public function api_get_transaction_history(Request $request)
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

        $transactions = Transactions::where('member_id', $member_record->id)->select('created_at','description','debit','credit','type');




        $records = $transactions->paginate($perPage);
        return response()->json([
            'message' => count($records) . ' Transactions Found ',
            'records' => $records
        ], 201);
    }


    public function api_get_wallet_summary(Request $request)
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



        $token = $request->token;
        $member_record = Members::where('token', '=', $token)->first();
        if (!$member_record || empty($token)) {
            return response()->json([
                'message' => 'invalid token'
            ], 400);
        }



        $topup = Transactions::where(['member_id' => $member_record->id, 'trans_purpose' => 0])->sum('debit');
        $used_purchase = Transactions::where(['member_id' => $member_record->id, 'trans_purpose' => 1])->sum('credit');
        $withdrawal = Transactions::where(['member_id' => $member_record->id, 'trans_purpose' => 2])->sum('credit');


        return response()->json([
            'message' =>  ' Success',
            'balance' => $member_record->balance,
            'topup' => $topup,
            'used_purchase' => $used_purchase,
            'withdrawal' => $withdrawal
        ], 201);
    }

}
