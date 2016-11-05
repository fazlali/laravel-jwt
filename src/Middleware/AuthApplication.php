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

//        if (! $this->auth->setRequest($request)) {
//            abort(403);
//        }

        if($request->getMethod() == 'OPTIONS'){

            $origin = '*';
            $response = $next($request);
        }else {
//            header('Access-Control-Allow-Origin: *');

            if (!JWTAPI::check()) {
                abort(403);
            }

            if ($origin = $request->headers->get('origin')) {

                if (!JWTAPI::application()->origins()->where('host', $origin)->first()) {
                    abort(403);
                }



            }
            $response = $next($request);
        }
        $headers = [
            'Access-Control-Allow-Origin' => $origin,
            'Access-Control-Allow-Methods' => 'POST, GET, OPTIONS, PUT, DELETE',
            'Access-Control-Allow-Headers' => 'Origin, X-Requested-With, Content-Type, Accept, Key, Authorization',
            'Access-Control-Allow-Credentials' => 'true'
        ];

        foreach ($headers as $key => $value)
            $response->header($key, $value);
        return $response;




    }
}
