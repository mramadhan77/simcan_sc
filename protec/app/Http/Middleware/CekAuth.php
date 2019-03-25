<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Session;

class CekAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $menu_id)
    {
        if(Auth::check()==false){
           return view ( 'errors.401' ); 
        };

        if(Session::has('tahun')==false){
           return redirect('home'); 
        }

        return $next($request);
    }
}
