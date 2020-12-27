<?php

namespace App\Http\Controllers\Personnel;

use App\Http\Controllers\Controller;
use App\Models\DeletedUser;
use App\Models\Institution;
use App\Models\User;
use App\Models\UserSocialite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class EmployeeController extends Controller
{
    /**
     * 校验当前账号是否有权限访问本网站下的员工
     *
     * @param Institution $institution
     * @return void
     */
    protected function validateInstitution(Institution $institution)
    {
        if ($institution->id != $this->user->institution_id && !$this->user->hasPermissionTo(Permission::findByName('manager', 'api')) && $institution->enterprise_id != $this->user->enterprise_id) {
            abort(404);
        }
    }

    /**
     * 拉取员工
     *
     * @param Institution $institution 网站
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Institution $institution, Request $request)
    {
        $this->validateInstitution($institution);
        $employees = $institution->users()->withTrashed()->withCount(['conversations',])->with(['permissions',])->paginate();

        return response()->success([
            'list' => $employees,
        ]);
    }

    /**
     * 添加员工
     *
     * @param Institution $institution
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Institution $institution, Request $request)
    {
        $request->validate([
            'name' => ['nullable', 'string',],
            'email' => ['nullable', 'email',],
            'password' => ['nullable', 'string', 'min:6', 'max:16'],
        ]);
        $this->validateInstitution($institution);
        if (!$this->user->hasPermissionTo(Permission::findByName('manager', 'api'))) {
            abort(403);
        }

        try {
            DB::beginTransaction();
            $user = new User([
                'name' => $request->input('name'),
                'password' => bcrypt($request->input('password')),
            ]);
            $user->institution()->associate($institution);
            $user->enterprise()->associate($institution->enterprise);
            $user->save();
            $user->givePermissionTo(Permission::findByName('support', 'api'));

            $userSocialite = new UserSocialite([
                'type' => UserSocialite::TYPE_EMAIL,
                'account' => $request->input('email'),
                'verified_at' => null,
            ]);
            $userSocialite->user()->associate($user);
            $userSocialite->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return response()->success([
            'employee' => $user,
        ])->setStatusCode(201);
    }

    /**
     * 展示员工
     *
     * @param Institution $institution
     * @param DeletedUser $user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Institution $institution, DeletedUser $user, Request $request)
    {
        $this->validateInstitution($institution);
        if ($user->institution_id != $institution->id) {
            abort(404);
        }

        return response()->success([
            'employee' => $user,
        ])->setStatusCode(201);
    }

    /**
     * 更新员工
     *
     * @param Institution $institution
     * @param DeletedUser $user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Institution $institution, DeletedUser $user, Request $request)
    {
        $request->validate([
            'name' => ['nullable', 'string',],
            'email' => ['nullable', 'email',],
            'password' => ['nullable', 'string', 'min:6', 'max:16'],
        ]);
        $this->validateInstitution($institution);
        if (!$this->user->hasPermissionTo(Permission::findByName('manager', 'api'))) {
            abort(403);
        }
        if ($user->institution_id != $institution->id) {
            abort(404);
        }

        try {
            DB::beginTransaction();
            if ($request->input('name')) {
                $user->fill(['name' => $request->input('name'),]);
            }
            if ($request->input('password')) {
                $user->fill(['password' => bcrypt($request->input('password')),]);
            }
            if ($request->input('email')) {
                $userSocialite = $user->userSocialites()->where('type', UserSocialite::TYPE_EMAIL)->first();
                if (strtolower($userSocialite->account) != strtolower($request->input('email'))) {
                    if (!$userSocialite) {
                        $userSocialite = new UserSocialite([
                            'type' => UserSocialite::TYPE_EMAIL,
                            'verified_at' => null,
                        ]);
                        $userSocialite->user()->associate($user);
                    }
                    $userSocialite->fill(['account' => $request->input('email'),]);
                    $userSocialite->save();
                }
            }
            $user->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return response()->success([
            'employee' => $user,
        ]);
    }

    /**
     * 禁用员工
     *
     * @param Institution $institution
     * @param DeletedUser $user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deactivate(Institution $institution, DeletedUser $user, Request $request)
    {
        $this->validateInstitution($institution);
        if (!$this->user->hasPermissionTo(Permission::findByName('manager', 'api'))) {
            abort(403, '无法禁用管理员');
        }
        if ($user->institution_id != $institution->id) {
            abort(404);
        }
        if ($user->id == $this->user->id) {
            abort(400, '无法禁用自己');
        }
        $user->delete();

        return response()->success([
            'employee' => $user,
        ]);
    }

    /**
     * 恢复被禁用员工
     *
     * @param Institution $institution
     * @param DeletedUser $user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function activate(Institution $institution, DeletedUser $user, Request $request)
    {
        $this->validateInstitution($institution);
        if (!$this->user->hasPermissionTo(Permission::findByName('manager', 'api'))) {
            abort(403);
        }
        if ($user->institution_id != $institution->id) {
            abort(404);
        }

        $user->restore();

        return response()->success([
            'employee' => $user,
        ]);
    }

    /**
     * 修改客服的密码
     *
     * @param Institution $institution
     * @param DeletedUser $user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(Institution $institution, DeletedUser $user, Request $request)
    {
        $request->validate(['password' => 'required|min:6|confirmed',]);
        $this->validateInstitution($institution);
        if (!$this->user->hasPermissionTo(Permission::findByName('manager', 'api'))) {
            abort(403);
        }
        if ($user->institution_id != $institution->id) {
            abort(404);
        }

        $user->fill(['password' => bcrypt($request->input('password'))]);
        $user->save();

        return response()->success([
            'employee' => $user,
        ]);
    }

    /**
     * 修改客服的权限
     *
     * @param Institution $institution
     * @param User $user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePermission(Institution $institution, User $user, Request $request)
    {
        $request->validate(['permission' => ['required', 'exists:permissions,name' ]]);
        $this->validateInstitution($institution);
        if (!$this->user->hasPermissionTo(Permission::findByName('manager', 'api'))) {
            abort(403);
        }
        if ($user->institution_id != $institution->id) {
            abort(404);
        }

        $permission = Permission::findByName($request->input('permission', 'api'));
        $user->syncPermissions($permission);

        return response()->success([
            'employee' => $user,
        ]);
    }
}
