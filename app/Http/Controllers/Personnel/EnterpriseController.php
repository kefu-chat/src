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
            'geographic.province.key' => ['nullable', 'integer'],
            'geographic.city.key' => ['nullable', 'integer'],
            'geographic.area.key' => ['nullable', 'integer'],
            'geographic.street.key' => ['nullable', 'integer'],
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
        $enterprise->save();
        return response()->success(['enterprise' => $enterprise]);
    }
}
