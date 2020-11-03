<?php
namespace App\Http\Controllers\Rocket;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ChannelController extends RocketBaseController
{
    public function list(Request $request)
    {
        return response()->json([
            'success' => true,
            "offset" => 0,
            "count" => 1,
            "total" => 1,
            'channels' => [
                [
                    "_id" => "ByehQjC44FwMeiLbX",
                    "name" => "invite-me",
                    "t" => "c",
                    "usernames" => [
                        "testing1",
                    ],
                    "msgs" => 0,
                    "u" => [
                        "_id" => "aobEdbYhXfu5hkeqG",
                        "username" => "testing1"
                    ],
                    "ts" => "2016-12-09T15:08:58.042Z",
                    "ro" => false,
                    "sysMes" => true,
                    "_updatedAt" => "2016-12-09T15:22:40.656Z"
                ]
            ],
        ]);
    }

    public function listJoined(Request $request)
    {
        return response()->json([
            'success' => true,
            'channels' => [
                [
                    "_id" => "ByehQjC44FwMeiLbX",
                    "name" => "invite-me",
                    "t" => "c",
                    "usernames" => [
                        "testing1",
                    ],
                    "msgs" => 0,
                    "u" => [
                        "_id" => "aobEdbYhXfu5hkeqG",
                        "username" => "testing1"
                    ],
                    "ts" => "2016-12-09T15:08:58.042Z",
                    "ro" => false,
                    "sysMes" => true,
                    "_updatedAt" => "2016-12-09T15:22:40.656Z"
                ]
            ],
        ]);
    }
}
