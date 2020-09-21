<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;
use Tymon\JWTAuth\JWT;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * 用户
     *
     * @var \App\Models\User|\App\Models\Visitor
     */
    protected $user;

    /**
     * {@inheritDoc}
     */
    public function callAction($method, $parameters)
    {
        if (JWTAuth::getToken()) {
            $this->user = auth()->user();
        }
        return parent::callAction($method, $parameters);
    }
}
