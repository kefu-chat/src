<?php

namespace App\Http\Controllers\Personnel;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use Illuminate\Http\Request;

class VisitorController extends Controller
{
    /**
     * Update visitor
     *
     * @param  Visitor $visitor
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Visitor $visitor, Request $request)
    {
        $request->validate([
            'name' => ['nullable', 'string',],
            'email' => ['nullable', 'email',],
            'phone' => ['nullable', 'string',],
            'memo' => ['nullable', 'string',],
        ]);

        if ($this->user->institution_id != $visitor->institution_id) {
            abort(404);
        }

        $visitor->fill($request->only(['name', 'email', 'phone', 'memo',]));
        $visitor->save();

        return response()->success([
            'visitor' => $visitor,
        ]);
    }
}
