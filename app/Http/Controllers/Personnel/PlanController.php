<?php

namespace App\Http\Controllers\Personnel;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Plan;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
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
            'plans_available' => Plan::where('available', '>', 0)->where('id', '<>', $this->user->enterprise->plan_id)->get(),
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
        $request->validate([
            'period' => ['required', 'string', 'in:' . collect(Order::PERIOD_MAP)->keys()->implode(','),],
            'counpon' => ['nullable', 'string',],
        ]);
        if ($plan->id == $this->user->enterprise->plan_id) {
            throw ValidationException::withMessages([
                'plan' => 'Current plan can\'t equals target plan',
            ]);
        }
        $price = $plan->{'price_' . $request->input('period')};

        DB::beginTransaction();

        try {
            $order = new Order();
            $order->user()->associate($this->user);
            $order->enterprise()->associate($this->user->enterprise);
            $order->plan()->associate($plan);
            $order->fill([
                'period' => $request->input('period'),
                'price' => $price,
                'need_pay_price' => $price,
                'status' => Order::STATUS_UNPAID,
            ]);
            if ($request->input('coupon')) {
                // 优惠券逻辑
                $coupon = Coupon::findPublicIdOrFail($request->input('coupon'));
                $order = $coupon->usingOnOrder($order);
                $coupon->save();
            }
            $order->save();
        } catch (Exception $e) {
            DB::commit();
            throw $e;
        }

        DB::commit();

        return response()->success([
            'order' => $order,
        ]);
    }

    /**
     * 支付婊支付付款
     * @param Order $order
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function alipay(Order $order, Request $request)
    {
        return response()->success([
            'pay' => $order->alipay
        ]);
    }

    /**
     * 微信支付付款
     * @param Order $order
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function wechatpay(Order $order, Request $request)
    {
        return response()->success([
            'pay' => $order->wechatpay
        ]);
    }
}
