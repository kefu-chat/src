<?php

namespace App\Http\Controllers\Personnel;

use App\Http\Controllers\Controller;
use App\Models\UserSocialite;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Get User
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function info(Request $request)
    {
        $user = $this->user;
        foreach ($user->userSocialites as $socialite) {
            $user->{$socialite->type} = $socialite->account;
        }

        return response()->success([
            'user' => $user,
            'institution' => $this->user->institution,
        ]);
    }
}
