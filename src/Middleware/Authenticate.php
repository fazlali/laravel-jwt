<?php

namespace Fazlali\LaravelJWT\Middleware;


use Fazlali\LaravelJWT\Facades\JWTAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
//    protected $auth;
    public function __construct(){
//        $this->auth = new JWTAuth();
    }
    public function handle(Request $request, \Closure $next)
    {
        if (!$user = JWTAuth::user()) {
            return response('Unauthorized.', 401);
        }
        Auth::login($user);

        return $next($request);
    }
}
