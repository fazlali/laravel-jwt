<?php

namespace Fazlali\LaravelJWT;
//use Fazlali\LaravelJWT\JWS;
use Illuminate\Http\Request;

class JWTAPI
{
    protected $application;
    protected $userId;
    protected $userPermissions;
    protected $jws;
    protected $request;
    protected $applicationModel;
    protected $auth;
    protected $issuer;
    protected $ttl;

    public function __construct(Request $request)
    {
        $this->applicationModel = app(config('laravel-jwt.api.models.application'));
        $this->jws = new JWS(['alg' => config('laravel-jwt.algo')]);
        $this->issuer = config('app.name', config('app.url'));
        $this->ttl = config('laravel-jwt.ttl', 60) *60;
        $this->setRequest ($request);
    }



    public function application(){
        $this->check();
        return $this->application;
    }

    public function userId(){
        $this->check();
        return $this->userId;
    }

    public function userPermissions(){
        $this->check();
        return $this->userPermissions;
    }

    public function token(){
        $requestHeader = $this->request->headers->get('authorization');

        if(! starts_with(strtolower($requestHeader), 'bearer')){
            return false;
        }

        if(! $token = trim(substr($requestHeader, strlen('bearer')))){
            return false;
        }

        return $token;


    }

    public function setRequest($request){
        $this->request = $request;

        if(! $token = $this->token())
            return false;
        $this->jws = JWS::load($token);
        return $this;
    }

    public function check(){

        if(! $token = $this->token())
            return false;
        if(count($token_parts = explode('.', $token)) !== 3){
            return false;
        }
        $header = json_decode(base64_decode($token_parts[0]));
        $payload = json_decode(base64_decode($token_parts[1]));
        if(! $application = $this->applicationModel->where('name',$payload->aud)->first()){
            return false;
        }

        $this->jws->setPayload((array)$payload);
        $this->jws->setHeader((array)$header);
        if(! $result =  $this->jws->isValid($application->secret,$header->alg)){
            return false;
        }
        $this->application = $application;
        $this->userId = property_exists($payload->sub, 'user') ? $payload->sub->user : null;
        $this->userPermissions = property_exists($payload->sub, 'permissions') ? $payload->sub->permissions : [];
        return true;
    }

    public function forApplication($application, $userId = null, $permissions = [])
    {
        $sub = [];
        if($userId) {
            $sub['user'] = $userId;
        }
        if (is_array($permissions) && count($permissions) > 0){
            foreach ($permissions as $permission) {
                if(is_string($permission))
                    $sub['permissions'][] = $permission;
            }
        }

        $this->jws->setPayload([
            'iss' => $this->issuer,
            'aud' => $application->name,
            'sub' => $sub,
            'iat' => time(),
            'nbf' => time(),
            'exp' => time() + $this->ttl
        ]);
        $this->jws->sign($application->secret);
        return $this->jws->getTokenString();
    }
}
