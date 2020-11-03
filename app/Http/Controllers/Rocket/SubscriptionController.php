<?php
namespace App\Http\Controllers\Rocket;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubscriptionController extends RocketBaseController
{
    public function get(Request $request)
    {
        return '{"update":[{"_id":"rduTbNAe2S7NbgC7x","open":true,"alert":false,"unread":0,"userMentions":0,"groupMentions":0,"ts":"2020-10-30T04:17:58.177Z","rid":"ag7Cs76AqbsDs4aNm","name":"chat","fname":"chat","t":"p","u":{"_id":"ZoDTNEi6yHkn7hTQc","username":"admin"},"ls":"2020-11-02T15:32:06.346Z","_updatedAt":"2020-10-30T04:46:35.217Z","roles":["owner"]},{"_id":"EEGEieD668FZTY7bX","open":true,"alert":true,"unread":0,"userMentions":0,"groupMentions":0,"ts":"2020-10-30T04:50:55.934Z","rid":"GENERAL","name":"general","t":"c","u":{"_id":"ZoDTNEi6yHkn7hTQc","username":"admin","name":"admin"},"_updatedAt":"2020-11-03T06:07:24.331Z","ls":"2020-11-03T05:22:41.645Z"}],"remove":[],"success":true}';
    }
}
