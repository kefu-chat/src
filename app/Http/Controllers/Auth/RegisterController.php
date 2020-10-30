<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ValidateCaptcha;
use App\Models\Enterprise;
use App\Models\Institution;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;

class RegisterController extends Controller
{
    use RegistersUsers, ValidateCaptcha;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    protected function registered(Request $request, User $user)
    {
        if ($user instanceof MustVerifyEmail) {
            return response()->json(['status' => trans('verification.sent')]);
        }

        return response()->json($user);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $captcha_answer = $data['captcha_answer'];
        $captcha_challenge = $data['captcha_challenge'];

        $this->validateCaptcha($captcha_challenge, $captcha_answer);

        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        try {
            DB::beginTransaction();
            $enterprise = new Enterprise([
                'name' => '未命名企业',
                'geographic' => [
                    'province' => [
                        'key' => 0,
                    ],
                    'city' => [
                        'key' => 0,
                    ],
                ]
            ]);
            $enterprise->save();

            $institution = new Institution([
                'name' => '未命名网站',
                'website' => null,
            ]);
            $institution->enterprise()->associate($enterprise);
            $institution->save();

            $user = new User([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
            ]);
            $user->institution()->associate($institution);
            $user->enterprise()->associate($enterprise);
            $user->save();
            $user->givePermissionTo(Permission::findByName('manager', 'api'));
            DB::commit();
            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
