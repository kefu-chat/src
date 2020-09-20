<?php

namespace App\Http\Controllers\Personnel;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PlanController extends Controller
{
    /**
     * Get Institution's plan
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $plan = $this->user->enterprise->plan;

        return response()->success([
            'plan' => $plan,
        ]);
    }

    /**
     * Get Institution's plan
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function upgrade(Plan $plan, Request $request)
    {
        if ($plan->id == $this->user->enterprise->plan_id) {
            throw ValidationException::withMessages([
                'plan' => 'Current plan can\'t equals target plan',
            ]);
        }

        return response()->success([
            'plan' => $plan,
        ]);
    }
}
