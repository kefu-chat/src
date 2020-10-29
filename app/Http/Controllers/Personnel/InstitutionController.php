<?php

namespace App\Http\Controllers\Personnel;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class InstitutionController extends Controller
{
    /**
     * List institution
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        $list = $this->user->enterprise->institutions()->withCount(['users',])->paginate();

        return response()->success([
            'list' => $list,
        ]);
    }

    /**
     * Create institution
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'website' => ['required', 'url',],
            'name' => ['required', 'string',],
        ]);
        if (!collect([null, '/'])->contains(data_get(parse_url($request->input('website')), 'path'))) {
            throw ValidationException::withMessages([
                'website' => '网站地址不允许为二级目录',
            ]);
        }

        $request->merge([
            'terminate_manual' => $request->input('terminate_manual') ?? Institution::DEFAULT['terminate_manual'],
            'terminate_timeout' => $request->input('terminate_timeout') ?? Institution::DEFAULT['terminate_timeout'],
            'greeting_message' => $request->input('greeting_message') ?? Institution::DEFAULT['greeting_message'],
            'technical_name' => $request->input('technical_name') ?? Institution::DEFAULT['technical_name'],
            'technical_phone' => $request->input('technical_phone') ?? Institution::DEFAULT['technical_phone'],
            'billing_name' => $request->input('billing_name') ?? Institution::DEFAULT['billing_name'],
            'billing_phone' => $request->input('billing_phone') ?? Institution::DEFAULT['billing_phone'],
            //'timeout' => $request->input('timeout') ?? Institution::DEFAULT['timeout'],
            'theme' => $request->input('theme') ?? Institution::DEFAULT['theme'],
        ]);
        $institution = new Institution();
        $institution->fill($request->only([
            'name',
            'website',
            'terminate_manual',
            'terminate_timeout',
            'greeting_message',
            'technical_name',
            'technical_phone',
            'billing_name',
            'billing_phone',
            //'timeout',
            'theme',
        ]));
        $institution->enterprise()->associate($this->user->enterprise);
        $institution->save();

        return response()->success([
            'institution' => $institution,
        ]);
    }

    /**
     * Get institution
     *
     * @param Institution $institution
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function show(Institution $institution, Request $request)
    {
        $user = $this->user;
        if ($user->enterprise_id != $institution->enterprise_id) {
            abort(404);
        }
        $institution->load(['users']);

        return response()->success([
            'institution' => $institution,
        ]);
    }

    /**
     * Get institution
     *
     * @param Institution $institution
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Institution $institution, Request $request)
    {
        $request->validate([
            'website' => ['required', 'url',],
            'name' => ['required', 'string',],
        ]);
        if (!collect([null, '/'])->contains(data_get(parse_url($request->input('website')), 'path'))) {
            throw ValidationException::withMessages([
                'website' => '网站地址不允许为二级目录',
            ]);
        }

        $user = $this->user;
        if ($user->enterprise_id != $institution->enterprise_id) {
            abort(404);
        }

        $institution->fill($request->only([
            'name',
            'website',
            'terminate_manual',
            'terminate_timeout',
            'greeting_message',
            'technical_name',
            'technical_phone',
            'billing_name',
            'billing_phone',
            'timeout',
            'theme',
        ]));
        $institution->save();

        return response()->success([
            'institution' => $institution,
        ]);
    }

    /**
     * Delete institution
     *
     * @param Institution $institution
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function delete(Institution $institution, Request $request)
    {
        $user = $this->user;
        if ($user->enterprise_id != $institution->enterprise_id) {
            abort(404);
        }

        $institution->delete();

        return response()->success([]);
    }
}
