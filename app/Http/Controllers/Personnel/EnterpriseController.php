<?php

namespace App\Http\Controllers\Personnel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EnterpriseController extends Controller
{
    /**
     * 企业资料
     */
    public function show(Request $request)
    {
        return response()->success(['enterprise' => $this->user->enterprise]);
    }

    /**
     * 企业资料
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => ['nullable', 'string'],
            'serial' => ['nullable', 'string'],
            'profile' => ['nullable', 'string'],
            'country' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string'],
            'geographic' => ['nullable', 'array'],
            'geographic.province' => ['nullable', 'string'],
            'geographic.city' => ['nullable', 'string'],
        ]);
        $enterprise = $this->user->enterprise;
        $enterprise->fill($request->only([
            'name',
            'serial',
            'profile',
            'country',
            'address',
            'phone',
            'geographic',
        ]));
        return response()->success(['enterprise' => $enterprise]);
    }
}
