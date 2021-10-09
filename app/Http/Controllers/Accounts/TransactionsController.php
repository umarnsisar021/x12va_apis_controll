<?php
namespace App\Http\Controllers\Accounts;

use App\Models\Accounts\Transactions;
use App\Models\Tasks\Tasks_proposals;
use App\Models\Tasks\Tasks;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Members\Members;
use Illuminate\Support\Facades\DB;

use Stripe\Stripe;
use Validator;

class TransactionsController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('can:accounts/ledger-view')->only(['get_wallet_data']);
        $this->middleware('can:accounts/wallet-view')->only(['ledger']);
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
//            ->leftjoin('transactions', 'transactions.member_id', '=', 'members.id')
            ->leftJoin('transactions', function ($join) {
                $join->on('members.id', '=', 'transactions.member_id');
                $join->on('transactions.status', '=', DB::raw('1'));
            })

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


    public function api_add_payment_by_proposal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'card_holder_name' => 'required',
            'card_no' => 'required',
            'exp_year' => 'required',
            'exp_month' => 'required',
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
                'message' => 'invalid token',
                'status' => 405
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


            //Stripe
            $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));

            try {
                $token = $stripe->tokens->create([
                    'card' => [
                        'number' => $request->card_no,
                        'exp_month' => $request->exp_month,
                        'exp_year' => $request->exp_year,
                        'cvc' => $request->cw_code,
                    ],
                ]);


                if (!isset($token['id'])) {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'The stripe token was not generated correctly!'
                    ], 404);
                }

                if (empty($member_record->stripe_id)) {
                    $customer = $stripe->customers->create([
                        'email' => $member_record->email,
                        'source' => $token['id'],
                    ]);
                    Members::where('id', $member_record->id)->update(['stripe_id' => $customer['id']]);
                    $member_record->stripe_id = $customer['id'];
                }

                $charge = $stripe->charges->create([
                    'customer' => $member_record->stripe_id,
                    'currency' => 'GBP',
                    'amount' => $tasks_proposal->budget * 100,
                    'description' => 'Payment for wallet add'
                ]);

                if ($charge['status'] == 'succeeded') {
                    DB::commit();

                } else {
                    DB::rollBack();

                    return response()->json([
                        'message' => 'Error in Transaction!'
                    ], 404);
                }
            } catch (Exception $e) {
                DB::rollBack();

                return response()->json([
                    'message' => $e->getMessage()
                ], 404);
            }
            //Stripe


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


    public function api_add_payment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'card_holder_name' => 'required',
            'card_no' => 'required',
            'exp_year' => 'required',
            'exp_month' => 'required',
            'cw_code' => 'required',
            'amount' => 'required'
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
                'message' => 'invalid token',
                'status' => 405
            ], 400);
        }


        DB::beginTransaction();
        $transaction = Transactions::create(
            [
                'primary_id' => $member_record->id,
                'primary_table' => (new Members())->getTable(),
                'type' => 0,
                'status' => 1,
                'debit' => $request->amount,
                'member_id' => $member_record->id,
                'description' => 'Add Card Payment',
                'trans_purpose' => 0
            ]
        );

        if ($transaction) {
            //Stripe
            $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
            try {
                $token = $stripe->tokens->create([
                    'card' => [
                        'number' => $request->card_no,
                        'exp_month' => $request->exp_month,
                        'exp_year' => $request->exp_year,
                        'cvc' => $request->cw_code,
                    ],
                ]);


                if (!isset($token['id'])) {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'The stripe token was not generated correctly!'
                    ], 404);
                }

                if (empty($member_record->stripe_id)) {
                    $customer = $stripe->customers->create([
                        'email' => $member_record->email,
                        'source' => $token['id'],
                    ]);
                    Members::where('id', $member_record->id)->update(['stripe_id' => $customer['id']]);
                    $member_record->stripe_id = $customer['id'];
                }

                $charge = $stripe->charges->create([
                    'customer' => $member_record->stripe_id,
                    'currency' => 'GBP',
                    'amount' => $request->amount * 100,
                    'description' => 'Payment for wallet add'
                ]);

                if ($charge['status'] == 'succeeded') {
                    DB::commit();

                } else {
                    DB::rollBack();

                    return response()->json([
                        'message' => 'Error in Transaction!'
                    ], 404);
                }
            } catch (Exception $e) {
                DB::rollBack();

                return response()->json([
                    'message' => $e->getMessage()
                ], 404);
            }
            //Stripe


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


    public function api_withdrawal(Request $request)
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
                'message' => 'invalid token',
                'status' => 405
            ], 400);
        }

        if($member_record->balance<=0){
            return response()->json([
                'message' => 'This value must be greater than or equal to 1.',
                'status' => 403
            ], 400);
        }
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));

        if (empty($member_record->stripe_connect_ac_id)) {
            $create_account = $stripe->accounts->create([
                'type' => 'express',
                'country' => 'GB',
                'email' => $member_record->email,
                'capabilities' => [
                    'card_payments' => ['requested' => true],
                    'transfers' => ['requested' => true],
                ],
            ]);
            Members::where('id', $member_record->id)
                ->update(['stripe_connect_ac_id'=> $create_account->id]);
            $member_record->stripe_connect_ac_id = $create_account->id;
        }
        try {
            //Stripe
            $data = $stripe->transfers->create([
                'amount' => $member_record->balance * 100,
                'currency' => 'GBP',
                'destination' => $member_record->stripe_connect_ac_id,
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            $data = $stripe->accountLinks->create([
                'account' => $member_record->stripe_connect_ac_id,
                'refresh_url' => 'http://web.salalkhan.com/stripe/reauth',
                'return_url' => 'http://web.salalkhan.com/stripe/return',
                'type' => 'account_onboarding',
            ]);
            return response()->json([
                'message' => $e->getMessage(),
                'verify_link' => $data,
                'status' => 402
            ], 404);
        }

        if ($data) {


            DB::beginTransaction();
            $transaction = Transactions::create(
                [
                    'primary_id' => $member_record->id,
                    'primary_table' => (new Members())->getTable(),
                    'type' => 1,
                    'status' => 1,
                    'credit' => $member_record->balance,
                    'member_id' => $member_record->id,
                    'description' => 'Withdrawal Amount',
                    'trans_purpose' => 2,
                    'reference_no' => $data->id
                ]
            );

            if ($transaction) {
                DB::commit();
                return response()->json([
                    'message' => 'Payment successfully added your stripe Account '
                ], 201);
            } else {
                DB::rollBack();

                return response()->json([
                    'message' => 'Error in Transaction!'
                ], 404);
            }


        } else {
            DB::rollBack();
            return response()->json([
                'message' => 'some thing wrong'
            ], 400);
        }


    }


    public function api_stripe_login(Request $request)
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
                'message' => 'invalid token',
                'status' => 405
            ], 400);
        }

        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        try {
            //Stripe
            //Express only
            $data = $stripe->accounts->createLoginLink(
                $member_record->stripe_connect_ac_id,
                []
            );

            return response()->json([
                'message' => 'Successfully created',
                'verify_link' => $data
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 404);
        }

    }


    public function api_payout()
    {

//        $stripe = Stripe::make(env('STRIPE_KEY'));
//        $stripe = new \Stripe\StripeClient(env('STRIPE_KEY'));
//        Stripe::setApiKey(env('STRIPE_SECRET'));
//        $stripe->charges()->create([
//            'currency' => 'GBP',
//            'amount'   => 10000,
//            'card'     => 4000000000000077
//        ]);

//        $stripe->transfers()->create([
//            'amount' => 40,
//            'currency' => 'gbp',
//            'destination' => 'acct_1JYy80RJiKX3x0dG',
//            'transfer_group' => 'ORDER_95',
//        ]);
        try {
            $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
//            $data = $stripe->accounts->create([
//                'type' => 'express',
//                'country' => 'GB',
//                'email' => 'salal.khan91@gmail.com',
//                'capabilities' => [
//                    'card_payments' => ['requested' => true],
//                    'transfers' => ['requested' => true],
//                ],
//            ]);
//
//            print_r($data->id);
//            die;


//        $data=  $stripe->accounts->retrieve(
//            'acct_1JZievRBx1iAuovE',
//            []
//        );

//        $data = $stripe->accounts->delete(
//            'acct_1JZhK9RRhmXMTiPt',
//            []
//        );


            //Express only
//        $data=     $stripe->accounts->createLoginLink(
//            'acct_1JeQ7bRPX7SrvoZm',
//            []
//        );

            //login to fill form
            $data = $stripe->accountLinks->create([
                'account' => 'acct_1JeQ7bRPX7SrvoZm',
                'refresh_url' => 'https://salalkhan.com/demo/x12va/reauth',
                'return_url' => 'https://salalkhan.com/demo/x12va/api/web/transaction/api_return',
                'type' => 'account_onboarding',
            ]);
            print_r($data);
            die;

//            $data=  $stripe->accounts->retrieve(
//                'acct_1Je2uORQF5UPs3wq',
//                []
//            );


            $data = $stripe->transfers->create([
                'amount' => 1,
                'currency' => 'gbp',
                'destination' => 'acct_1Je2uORQF5UPs3wq',
                'transfer_group' => 'ORDER_95',
            ]);
            print_r($data);
            die;


//        $data= $stripe->accounts->allCapabilities(
//            'acct_1JZiNMRF8WAYjjao',
//            []
//        );


//        $data = $stripe->accounts->create([
//            'type' => 'express',
//            'country' => 'GB',
//
//        ]);
//        $data =  $stripe->accountLinks->create([
//            'account' => 'acct_1Ja4CkRBB5jugxa3',
//            'refresh_url' => 'https://example.com/reauth',
//            'return_url' => 'http://localhost:8000/api/web/transaction/api_return',
//            'type' => 'account_onboarding',
//        ]);

            print_r($data);
            die;
        } catch (Exception $e) {
            print_r($e->getError()->message);
            die;
        }
    }

    public function api_return(Request $request)
    {
        print_r($request->all());
        echo "<br />";
        print_r(request()->all());
        echo "<br />";
        die;
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
                'message' => 'invalid token',
                'status' => 405
            ], 400);
        }

        $transactions = Transactions::where('member_id', $member_record->id)->select('created_at', 'description', 'debit', 'credit', 'type','status');


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
                'message' => 'invalid token',
                'status' => 405
            ], 400);
        }


        $topup = Transactions::where(['member_id' => $member_record->id, 'trans_purpose' => 0])->sum('debit');
        $used_purchase = Transactions::where(['member_id' => $member_record->id, 'trans_purpose' => 1])->sum('credit');
        $withdrawal = Transactions::where(['member_id' => $member_record->id, 'trans_purpose' => 2])->sum('credit');
        $pending_balance = Transactions::where(['member_id' => $member_record->id, 'trans_purpose' => 3,'status'=>0])->sum('debit');


        return response()->json([
            'message' => ' Success',
            'balance' => $member_record->balance,
            'pending_balance' => $pending_balance,
            'topup' => $topup,
            'used_purchase' => $used_purchase,
            'withdrawal' => $withdrawal,
        ], 201);
    }

}
