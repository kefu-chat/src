<?php

namespace App\Http\Controllers;

use Aoxiang\Pca\Models\ProvinceCityArea;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * 获取地理位置
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        $request->validate([
            'parent_id' => ['nullable', 'integer'],
        ]);

        $list = ProvinceCityArea::where('parent_id', $request->input('parent_id', 0))->get();

        return response()->success([
            'list' => $list,
        ]);
    }
}
