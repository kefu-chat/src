<?php
namespace App\Http\Controllers\Rocket;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RoomController extends RocketBaseController
{
    public function get(Request $request)
    {
        return '{"update":[{"_id":"GENERAL","t":"c","name":"general","usernames":[],"usersCount":3,"default":true,"_updatedAt":"2020-11-03T06:07:24.310Z","lastMessage":{"_id":"7FTILdBcQbRMX4HrQ","rid":"GENERAL","msg":"3","ts":"2020-11-03T06:07:24.229Z","u":{"_id":"MN7pNWixNopHmJCne","username":"xiaoreign","name":"lei"},"_updatedAt":"2020-11-03T06:07:24.300Z","mentions":[],"channels":[]},"lm":"2020-11-03T06:07:24.229Z"},{"_id":"ag7Cs76AqbsDs4aNm","name":"chat","fname":"chat","t":"p","usersCount":1,"u":{"_id":"ZoDTNEi6yHkn7hTQc","username":"admin"},"customFields":{},"broadcast":false,"encrypted":false,"ro":false,"default":false,"sysMes":true,"_updatedAt":"2020-10-30T04:46:35.219Z","lastMessage":{"_id":"rQPQC63puihvpqgHP","rid":"ag7Cs76AqbsDs4aNm","msg":"Yoxi","ts":"2020-10-30T04:46:35.112Z","u":{"_id":"ZoDTNEi6yHkn7hTQc","username":"admin","name":"admin"},"_updatedAt":"2020-10-30T04:46:35.207Z","mentions":[],"channels":[]},"lm":"2020-10-30T04:46:35.112Z"}],"remove":[],"success":true}';
    }
}
