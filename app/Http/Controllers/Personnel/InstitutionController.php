<?php

namespace App\Http\Controllers\Personnel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InstitutionController extends Controller
{
    /**
     * Get institution profile
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function showProfile(Request $request)
    {
        $user = auth()->user();
        $institution = $user->institution;

        return response()->success([
            'institution' => $institution,
        ]);
    }

    /**
     * Get institution profile
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $institution = $user->institution;
        $institution->fill($request->only(['name', 'website',]));
        $institution->save();

        return response()->success([
            'institution' => $institution,
        ]);
    }
}
