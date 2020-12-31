<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\UserSocialite;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Update the user's profile information.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $this->validate($request, [
            'name' => ['nullable', 'string'],
            'email' => ['required', 'email', 'max:255', Rule::unique(UserSocialite::class, 'account')->where('type', UserSocialite::TYPE_EMAIL)->whereNot('user_id', $user->id),],
            'title' => ['nullable', 'string'],
            'avatar' => ['nullable', 'url'],
        ]);
        tap($user)->update($request->only('name', 'email', 'title', 'avatar'));

        return response()->success([
            'user' => $user,
        ]);
    }
}
