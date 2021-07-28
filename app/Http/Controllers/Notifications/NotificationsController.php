<?php
namespace App\Http\Controllers\Notifications;

use App\Models\Clients\Clients;
use App\Models\Experts\Experts;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Notifications;
use App\Models\Members\Members;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Validator;

class NotificationsController extends Controller
{


    public function api_get_notifications(Request $request)
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

        DB::beginTransaction();
        $notifications = Notifications::where('member_id', $member_record->id)->orderByDesc('id')->limit(10);

        if (isset($request->new) && $request->new == 1) {
            $notifications->where('notify', 0);
        }

        if (isset($request->offset)) {
            $notifications->offset($request->offset);
        }
        $notifications = $notifications->get();

        foreach ($notifications as $index => $notification) {
            $member = Members::where('id', $notification->from_member_id)
                ->first();
            $from_detail = [];
            if ($member) {
                if ($member->is_buyer == 1) {
                    $from_detail = Clients::where('member_id', $member->id)->select('first_name', 'last_name', 'avatar')->first();
                }
                if ($member->is_seller == 1) {
                    $from_detail = Experts::where('member_id', $member->id)->select('first_name', 'last_name', 'avatar')->first();
                }
            }
            $notifications[$index]['from'] = $from_detail;
        }

        $un_view = Notifications::where(['member_id' => $member_record->id, 'view' => 0])->orderByDesc('id')->count();
        $un_notify = Notifications::where(['member_id' => $member_record->id, 'notify' => 0])->orderByDesc('id')->count();

        return response()->json([
            'message' => ' record found',
            'records' => $notifications,
            'un_view' => $un_view,
            'un_notify' => $un_notify

        ], 201);
    }


    public function api_set_notify_notification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'notification_id' => 'required'
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

        $notification = Notifications::where('id', '=', $request->notification_id)->first();
        if (!$notification) {
            return response()->json([
                'message' => 'Notification not found'
            ], 400);
        }


        DB::beginTransaction();
        $notification = Notifications::where('id', $notification->id)
            ->update(['notify' => 1]);

        if ($notification) {
            DB::commit();
            return response()->json([
                'message' => ' successfully Update'
            ], 201);
        } else {
            DB::rollBack();
            return response()->json([
                'message' => 'some thing wrong'
            ], 400);
        }
    }


    public function api_set_view_notification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'notification_id' => 'required'
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

        $notification = Notifications::where('id', '=', $request->notification_id)->first();
        if (!$notification) {
            return response()->json([
                'message' => 'Notification not found'
            ], 400);
        }


        DB::beginTransaction();
        $notification = Notifications::where('id', $notification->id)
            ->update(['view' => 1]);

        if ($notification) {
            DB::commit();
            return response()->json([
                'message' => ' successfully Update'
            ], 201);
        } else {
            DB::rollBack();
            return response()->json([
                'message' => 'some thing wrong'
            ], 400);
        }
    }


}
