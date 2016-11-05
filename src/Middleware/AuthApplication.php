<?php

namespace Fazlali\LaravelJWT\Middleware;


use Fazlali\LaravelJWT\Facades\JWTAPI;
use Illuminate\Http\Request;

class AuthApplication
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
        if(! $application = \JWTAPI::application()){
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT');
            header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Key, Authorization');
            header('Access-Control-Allow-Credentials: true');
            return response('Access denied.', 403);
        }
        if($origin = $request->headers->get('origin')){
            if(collect($application->origins)->contains($origin)){
                header('Access-Control-Allow-Origin: *');
                header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT');
                header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Key, Authorization');
                header('Access-Control-Allow-Credentials: true');
            }
        }
        return $next($request);


    }
}
