<?php

namespace App\Http\Controllers\Personnel;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use Illuminate\Http\Request;

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
        $institution = new Institution();
        $institution->fill($request->only(['name', 'website', ]));
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
        $user = $this->user;
        if ($user->enterprise_id != $institution->enterprise_id) {
            abort(404);
        }

        $institution->fill($request->only(['name', 'website',]));
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
