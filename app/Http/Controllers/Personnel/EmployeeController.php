<?php

namespace App\Http\Controllers\Personnel;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\User;
use Illuminate\Http\Request;
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
        $employees = $institution->users()->withTrashed()->withCount(['conversations',])->paginate();

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
        $request->input([
            'name' => ['nullable', 'string',],
            'email' => ['nullable', 'email',],
            'password' => ['nullable', 'string', 'min:6', 'max:16'],
        ]);
        $this->validateInstitution($institution);
        if (!$this->user->hasPermissionTo(Permission::findByName('manager', 'api'))) {
            abort(403);
        }

        $user = new User([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
        ]);
        $user->institution()->associate($institution);
        $user->enterprise()->associate($institution->enterprise);
        $user->save();
        $user->givePermissionTo(Permission::findByName('support', 'api'));

        return response()->success([
            'employee' => $user,
        ])->setStatusCode(201);
    }

    /**
     * 展示员工
     *
     * @param Institution $institution
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Institution $institution, User $user, Request $request)
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
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Institution $institution, User $user, Request $request)
    {
        $request->input([
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

        if ($request->input('name')) {
            $user->fill(['name' => $request->input('name'),]);
        }
        if ($request->input('email')) {
            $user->fill(['email' => $request->input('email'),]);
        }
        if ($request->input('password')) {
            $user->fill(['password' => bcrypt($request->input('password')),]);
        }
        $user->save();

        return response()->success([
            'employee' => $user,
        ]);
    }

    /**
     * 禁用员工
     *
     * @param Institution $institution
     * @param User $user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deactivate(Institution $institution, User $user, Request $request)
    {
        $this->validateInstitution($institution);
        if (!$this->user->hasPermissionTo(Permission::findByName('manager', 'api'))) {
            abort(403);
        }
        if ($user->institution_id != $institution->id) {
            abort(404);
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
     * @param User $user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function activate(Institution $institution, User $user, Request $request)
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
}
