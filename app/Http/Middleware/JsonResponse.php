<?php

namespace App\Http\Middleware;

use Closure;

class JsonResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    //by ma api返回json格式
    public function handle($request, Closure $next)
    {
        if ($request->is('api/*')){
            $request->headers->set('Accept', 'application/json');
        }
        return $next($request);
    }

}
