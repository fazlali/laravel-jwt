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

    public function __construct(Request $request)
    {
        $this->applicationModel = app(config('laravel-jwt.api.models.application'));
        $this->jws = new JWS(['alg' => config('laravel-jwt.algo')]);
        $this->issuer = config('app.name', config('app.url'));
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
        if(! $application = $this->applicationModel->where('name',$payload->sub)->first()){
            return false;
        }

        $this->jws->setPayload($payload);
        $this->jws->setHeader($header);
        if(! $result =  $this->jws->isValid($application->secret,$header->alg)){
            return false;
        }
        $this->application = $application;
        $this->userId = property_exists($payload->aub, 'user') ? $payload->aub->user : null;
        $this->userPermissions = property_exists($payload->aub, 'permissions') ? $payload->aub->permissions : [];
        return true;
    }

    public function forApplication($application, $userId = null, $permissions = [])
    {
        $aub = [];
        if($userId) {
            $aub['user'] = $userId;
        }
            if (is_array($permissions) && count($permissions) > 0){
                foreach ($permissions as $permission) {
                    if(is_string($permission))
                        $aub['permissions'][] = $permission;
                }
            }

        $this->jws->setPayload([
            'iss' => $this->issuer,
            'sub' => $application->name,
            'aub' => $aub,
            'iat' => time(),
            'nbf' => time(),
            'exp' => time() + $this->ttl
        ]);
        $this->jws->sign($application->secret);
        return $this->jws->getTokenString();
    }
}
