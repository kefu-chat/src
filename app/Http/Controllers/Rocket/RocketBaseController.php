<?php
namespace App\Http\Controllers\Rocket;

use App\Http\Controllers\Controller;

abstract class RocketBaseController extends Controller
{
    protected function getApiDomain()
    {
        return parse_url(config('app.url'))['host'];
    }
}
