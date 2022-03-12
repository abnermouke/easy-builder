<?php


namespace App\Http\Middleware\Abnermouke;

use Closure;
use Illuminate\Http\Request;

/**
 * Easy Builder Base Middleware Power By Abnermouke
 * Class EasyBuilderBaseMiddleware
 * @package Abnermouke\EasyBuilder\Middlewares
 */
class EasyBuilderBaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //记录请求时间
        $request->offsetSet('logic_request_log_time', time());

        //TODO ：其他中间件操作


        return $next($request);
    }
}
