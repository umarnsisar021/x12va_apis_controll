<?php
namespace App\Http\Controllers\Clients;

use App\Models\Experts\Experts;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Clients\Clients;
use App\Models\Members\Members;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Validator;
use Stripe\Stripe;

class ClientsController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->middleware('can:clients/clients-view')->only(['get_data', 'get', 'get_member_list_data']);
        $this->middleware('can:clients/clients-add')->only(['add']);
        $this->middleware('can:clients/clients-edit')->only(['update', 'changePassword']);
        $this->middleware('can:clients/clients-delete')->only(['delete']);
    }

    public function get_data(Request $request)
    {
        $perPage = request('perPage', 10);
        $search = request('q');
        $clients = Clients::orderByDesc('id');
        if (!empty($search)) {
            $clients->where('first_name', 'like', '%' . $search . '%')->orWhere('last_name', 'like', '%' . $search . '%');
        }

        if (!empty($role)) {
            //$user->where('role', $role);
        }

        $clients = $clients->paginate($perPage);
        return response()->json($clients);
    }

    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|between:2,100|unique:members',
            'password' => 'required|string|max:100',
            'email' => 'required|string|unique:members',
            'first_name' => 'required|string|between:2,100',
            'middle_name' => 'string|between:2,100',
            'last_name' => 'required|string|between:2,100',
            'd_o_b' => 'required|string',
            'gender' => 'required|string',
            'country' => 'required|string',
            'mobile_number' => 'required|string',
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

        $member = Members::create(
            [
                'username' => $request->username,
                'password' => bcrypt($request->password),
                'is_buyer' => 1,
                'email' => $request->email
            ]
        );


        $avatar_name = $request->username . '-' . $member->id;
        $avatar = '';


        if ($request->avatar) {
            $avatar = $this->uploadfile_to_s3($request->avatar, $avatar_name, 'avatars');
        }
        $validator_data = $validator->validated();
        unset($validator_data['username']);
        unset($validator_data['password']);
        $clients = Clients::create(array_merge(
            $validator_data,
            [
                'member_id' => $member->id,
                'avatar' => $avatar,
            ]
        ));

        if (!$clients) {
            DB::rollBack();
            return response()->json([
                'message' => 'info missing'
            ], 400);
        }
        try {
            $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
            $data = $stripe->accounts->create([
                'type' => 'express',
                'country' => 'GB',
                'email' => $request->email,
                'capabilities' => [
                    'card_payments' => ['requested' => true],
                    'transfers' => ['requested' => true],
                ],
            ]);
            Members::where('id', $member->id)
                ->update(['stripe_connect_ac_id'=>$data->id]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getError()->message
            ], 400);
        }

        DB::commit();
        return response()->json([
            'message' => 'Data successfully added',
            'record' => $clients
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

        $user = Clients::find($request['id']);
        $user->delete();
        return response()->json([
            'message' => 'Record successfully deleted'
        ], 201);
    }

    public function update(Request $request)
    {
        $rules = [
            'first_name' => 'required|string|between:2,100',
            'last_name' => 'required|string|between:2,100',
        ];
        $rules['id'] = ['required', 'exists:clients,id'];


        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            $validators = $validator->errors()->toArray();
            $data = [
                'validations' => $validators,
                'message' => $validator->errors()->first()
            ];
            return response()->json($data, 400);
        }

        $validator = $validator->validated();

        $id = $validator['id'];
        unset($validator['id']);
        Clients::where('id', $id)
            ->update($validator);

        return response()->json([
            'message' => 'Data successfully updated'
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

        $client = Clients::find($request['id']);
        $member = Members::find($client['member_id']);

        return response()->json([
            'message' => 'Get Record successfully',
            'member' => $member,
            'record' => $client
        ], 201);
    }


    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'new_password' => 'required',
        ]);
        if ($validator->fails()) {
            $validators = $validator->errors()->toArray();
            $data = [
                'validations' => $validators,
                'message' => $validator->errors()->first()
            ];
            return response()->json($data, 400);
        }
        $id = $request->id;
        Members::where('id', $id)
            ->update(['password' => bcrypt($request->new_password)]);
        return response()->json([
            'message' => 'Password successfully updated.'
        ], 201);
    }


    public function get_member_list_data(Request $request)
    {
        $members = Members::orderByDesc('members.id')
            ->select('members.id', 'clients.first_name', 'clients.last_name', 'clients.avatar')
            ->leftjoin('clients', 'clients.member_id', '=', 'members.id');

        $members = Members::orderByDesc('members.id')
            ->select('members.id', 'experts.first_name', 'experts.last_name', 'experts.avatar')
            ->leftjoin('experts', 'experts.member_id', '=', 'members.id')
            ->union($members);

        $members = $members->get();
        return response()->json($members);
    }


    public function api_login(Request $request)
    {


        if ($request->type == 0) {
            $validator = Validator::make($request->all(), [
                'email' => 'required',
                'password' => 'required|string|min:6',
            ]);
        } elseif ($request->type == 1) {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string',
                'first_name' => 'required|string|between:2,100',
                'last_name' => 'required|string|between:2,100',
                'avatar' => '',
                'google_id' => 'required|string'
            ]);
        } elseif ($request->type == 2) {
            $validator = Validator::make($request->all(), [
                'email' => 'string',
                'first_name' => 'required|string|between:2,100',
                'last_name' => 'required|string|between:2,100',
                'avatar' => 'required|string',
                'facebook_id' => 'required|string'
            ]);
        }

        if ($validator->fails()) {
            $validators = $validator->errors()->toArray();
            $data = [
                'validations' => $validators,
                'message' => $validator->errors()->first()
            ];
            return response()->json($data, 400);
        }

        if ($request->type == 0) {
            $member_record = Members::where('email', '=', $request->email)->OrWhere('username', '=', $request->email)->first();
            if ($member_record) {
                if (!Hash::check($request->password, $member_record->password)) {
                    return response()->json(['success' => false, 'message' => 'Login Fail, pls check password']);
                }

                $user_record = array();
                if ($member_record->is_seller == 1) {
                    $user_record = Experts::where('member_id', '=', $member_record->id)->first();
                }
                if ($member_record->is_buyer == 1) {
                    $user_record = Clients::where('member_id', '=', $member_record->id)->first();
                }

                $token = bin2hex(random_bytes(64));
                $user_info = array(
                    'id' => $member_record->id,
                    'first_name' => $user_record->first_name,
                    'last_name' => $user_record->last_name,
                    'email' => $user_record->email,
                    'is_seller' => $member_record->is_seller,
                    'is_buyer' => $member_record->is_buyer,
                    'mobile_number' => $user_record->mobile_number,
                    'username' => $member_record->username,
                    'avatar' => $user_record->avatar,
                );
                Members::where('id', $member_record->id)
                    ->update(['token' => $token]);
                return response()->json([
                    'message' => 'Account successfully login',
                    'token' => $token,
                    'user_info' => $user_info
                ], 201);
            } else {
                return response()->json([
                    'message' => 'wrong username or password'
                ], 400);
            }
        } elseif ($request->type == 1) {
            $member_record = Members::where('google_id', '=', $request->google_id)->get()->first();
            if ($member_record) {
                $token = bin2hex(random_bytes(64));
                $user_record = array();
                if ($member_record->is_seller == 1) {
                    $user_record = Experts::where('member_id', '=', $member_record->id)->get()->first();
                }
                if ($member_record->is_buyer == 1) {
                    $user_record = Clients::where('member_id', '=', $member_record->id)->get()->first();
                }

                $user_info = array(
                    'id' => $member_record->id,
                    'first_name' => $user_record->first_name,
                    'last_name' => $user_record->last_name,
                    'email' => $user_record->email,
                    'is_seller' => $member_record->is_seller,
                    'is_buyer' => $member_record->is_buyer,
                    'mobile_number' => $user_record->mobile_number,
                    'username' => $member_record->username,
                    'avatar' => $user_record->avatar,
                );
                Members::where('id', $member_record->id)
                    ->update(['token' => $token]);
                $new_register = 0;
                if (empty($member_record->username)) {
                    $new_register = 1;
                }
                return response()->json([
                    'message' => 'Account successfully login',
                    'token' => $token,
                    'user_info' => $user_info,
                    'new_register' => $new_register
                ], 201);
            } else {
                DB::beginTransaction();
                $member = Members::create(array_merge(
                    $validator->validated(),
                    [
                        'email' => $request->email,
                        'is_buyer' => 1,
                        'google_id' => $request->google_id,
                        'signup_with' => 1
                    ]
                ));

                $b64image = '';
                if (isset($request->avatar) && !empty($request->avatar)) {
                    $b64image = "data:image/jpg;base64," . base64_encode(file_get_contents($request->avatar));
                }
//                $file_name=$member->id.uniqid('img_', true);
                $avatar_name = 'user-' . $member->id;


                $avatar = $this->uploadfile_to_s3($b64image, $avatar_name, 'avatars');
                $clients = Clients::create(array_merge(
                    $validator->validated(),
                    [
                        'member_id' => $member->id,
                        'first_name' => $request->first_name,
                        'last_name' => $request->last_name,
                        'email' => $request->email,
                        'avatar' => $avatar
                    ]
                ));
                if (!$clients) {
                    DB::rollBack();
                    return response()->json([
                        'message' => 'info missing'
                    ], 400);
                }

                $token = bin2hex(random_bytes(64));
                $user_info = array(
                    'id' => $member->id,
                    'first_name' => $clients->first_name,
                    'last_name' => $clients->last_name,
                    'email' => $clients->email,
                    'is_seller' => $member->is_seller,
                    'is_buyer' => $member->is_buyer,
                    'mobile_number' => '',
                    'username' => '',
                    'avatar' => $clients->avatar,
                );
                Members::where('id', $member->id)
                    ->update(['token' => $token]);



                try {
                    $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
                    $data = $stripe->accounts->create([
                        'type' => 'express',
                        'country' => 'GB',
                        'email' => $request->email,
                        'capabilities' => [
                            'card_payments' => ['requested' => true],
                            'transfers' => ['requested' => true],
                        ],
                    ]);
                    Members::where('id', $member->id)
                        ->update(['stripe_connect_ac_id'=>$data->id]);
                } catch (Exception $e) {
                    return response()->json([
                        'message' => $e->getError()->message
                    ], 400);
                }



                DB::commit();

                return response()->json([
                    'message' => 'Account successfully login',
                    'token' => $token,
                    'user_info' => $user_info,
                    'new_register' => 1
                ], 201);
            }
        }

        return response()->json([
            'message' => 'info missing'
        ], 400);
    }

    public function api_set_username_or_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'username' => 'required|string|between:2,100|unique:members',
            'password' => 'required|string|between:6,100|max:100'
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
        $member = Members::where('id', $member_record->id)
            ->update(['username' => $request->username, 'password' => bcrypt($request->password)]);
        if ($member) {
            DB::commit();
            $clients = Clients::where('member_id', $member_record->id)->first();
            $user_info = array(
                'id' => $member_record->id,
                'first_name' => $clients->first_name,
                'last_name' => $clients->last_name,
                'email' => $clients->email,
                'is_seller' => $member_record->is_seller,
                'is_buyer' => $member_record->is_buyer,
                'mobile_number' => '',
                'username' => '',
                'avatar' => $clients->avatar,
            );
            return response()->json([
                'message' => 'Account successfully login',
                'token' => $token,
                'user_info' => $user_info
            ], 201);

        } else {
            DB::rollBack();
            return response()->json([
                'message' => 'some thing wrong'
            ], 400);
        }
    }


    public function api_register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|between:2,100|unique:members',
            'password' => 'required|string|max:100',
            'email' => 'required|string|unique:members',
            'first_name' => 'required|string|between:2,100',
            'last_name' => 'required|string|between:2,100',
            'd_o_b' => 'required|string',
            'gender' => 'required|string'
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
        $member = Members::create(array_merge(
            $validator->validated(),
            [
                'username' => $request->username,
                'email' => $request->email,
                'is_buyer' => 1,
                'password' => bcrypt($request->password)
            ]
        ));


        $clients = Clients::create([
            'member_id' => $member->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'd_o_b' => $request->d_o_b,
            'gender' => $request->gender,
            'email' => $request->email
        ]);
        if (!$clients) {
            DB::rollBack();
            return response()->json([
                'message' => 'info missing'
            ], 400);
        }

        try {
            $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
            $data = $stripe->accounts->create([
                'type' => 'express',
                'country' => 'GB',
                'email' => $request->email,
                'capabilities' => [
                    'card_payments' => ['requested' => true],
                    'transfers' => ['requested' => true],
                ],
            ]);
            Members::where('id', $member->id)
                ->update(['stripe_connect_ac_id'=>$data->id]);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getError()->message
            ], 400);
        }

        DB::commit();
        return response()->json([
            'message' => 'Account successfully created'
        ]);

    }


    public function api_logout(Request $request)
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

        DB::beginTransaction();
        $member = Members::where('id', $member_record->id)
            ->update(['token' => null]);

        if ($member) {

            DB::commit();
            return response()->json([
                'message' => 'logout successfully'
            ], 201);

        } else {
            DB::rollBack();
            return response()->json([
                'message' => 'some thing wrong'
            ], 400);
        }


    }

    public function api_change_profile(Request $request)
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

        $avatar_name = $member_record->username . '-' . $member_record->id;
        $avatar = '';
        if ($request->avatar) {
            $avatar = $this->uploadfile_to_s3($request->avatar, $avatar_name, 'avatars');
        } else {
            return response()->json([
                'message' => 'Avatar not found',
                'status' => 400
            ], 400);
        }

        DB::beginTransaction();
        $result = Experts::where('member_id', $member_record->id)
            ->update(['avatar' => $avatar]);
        $result = Clients::where('member_id', $member_record->id)
            ->update(['avatar' => $avatar]);
        if ($result) {
            DB::commit();
            return response()->json([
                'message' => 'Profile Successfully Update',
                'avatar'=>$avatar,
                'status' => 201
            ], 201);
        } else {
            DB::rollBack();
            return response()->json([
                'message' => 'Some thing wrong',
                'status' => 400
            ], 400);
        }


    }

}
