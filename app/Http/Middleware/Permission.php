<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Response;

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

        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $response = array(
                'status' => false,
                'message' => 'You dont have permission',
            );
            return Response::json($response);
        }
        else{
         return redirect('404'); 
     } 
 }
}
