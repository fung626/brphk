<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\User\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class NotificationController extends Controller
{
    //

    protected $types = [
        'summary',
    ];

    public function get(Request $request)
    {
        $user = Auth::user();
        $response = config('response.common.success');
        $notifications = Notification::where('user_id', $user->id)->get();
        $data = [];
        foreach ($this->types as $type) {
            foreach ($notifications as $notification) {
                if ($notification->type === $type) {
                    $data[$type] = $notification->enable;
                }
            }
        }
        $response['data'] = $data;
        return response()->json($response, 200);
    }

    public function update(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'summary' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            $response = config('response.common.fail.parameter');
            $response['data'] = $validator->errors();
            return response()->json($response, 400);
        }

        $user = Auth::user();

        try {
            Notification::updateOrCreate(
                ['user_id' => $user->id, 'type' => 'summary'],
                ['enable' => request('summary')]
            );
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error($e->getMessage());
            $response = config('response.common.fail.database');
            $response['msg'] = $e->getMessage();
            return response()->json($response, 500);
        }

        $notifications = Notification::where('user_id', $user->id)->get();
        $data = [];
        foreach ($this->types as $type) {
            foreach ($notifications as $notification) {
                if ($notification->type === $type) {
                    $data[$type] = $notification->enable;
                }
            }
        }

        $response = config('response.common.success');
        $response['data'] = $data;

        return response()->json($response, 200);
    }

}