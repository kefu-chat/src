<?php
namespace App\Http\Controllers\Rocket;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MethodCallController extends RocketBaseController
{
    public function licenseGetModules(Request $request)
    {
        return response()->json([
            'message' => '{"msg":"result","result":[]}',
            'success' => true,
        ]);
    }
}
