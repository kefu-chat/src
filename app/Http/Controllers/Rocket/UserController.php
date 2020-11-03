<?php
namespace App\Http\Controllers\Rocket;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends RocketBaseController
{
    public function presence(Request $request)
    {
        return '{"users":[{"_id":"ZoDTNEi6yHkn7hTQc","status":"online","name":"admin","username":"admin","utcOffset":8}],"full":false,"success":true}';
    }
}
