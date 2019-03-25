<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Session\Store;

class SessionTimeout {

    protected $session;
    protected $timeout = 600;

    public function __construct(Store $session){
        $this->session = $session;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $isLoggedIn = $request->path() != '/login';
        if(! session('lastActivityTime'))
            $this->session->put('lastActivityTime', time());
        elseif(time() - $this->session->get('lastActivityTime') > $this->timeout){
            $this->session->forget('lastActivityTime');
            $email = $request->user()->email;
            auth()->logout();
            return redirect('/login');
        }
        $isLoggedIn ? $this->session->put('lastActivityTime', time()) : $this->session->forget('lastActivityTime');
        return $next($request);
    }

}