<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use stdClass;

class SuccessServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function register()
    {
        response()->macro('success', function ($data = null) {
            if (!$data) {
                $data = new stdClass();
            }
            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        });

        response()->macro('error', function ($message, $code = -1, $statusCode = 400) {
            return response()->json([
                'success' => false,
                'code' => $code,
                'message' => $message,
            ]);
        });
    }
}
