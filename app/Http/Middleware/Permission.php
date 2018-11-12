<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Permission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        foreach(Auth::user()->role->permissions as $perm){
            $navs[] = $perm->navigation_code;
        }

        $action = $request->route()->getAction();

        if(in_array($action['nav'], $navs)){
            return $next($request);
        }

        return redirect('404');
    }
}
