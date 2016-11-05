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

    public function __construct(Request $request)
    {
        $this->applicationModel = app(config('jwt.api.models.application'));
        $this->jws = new JWS(['alg' => config('jwt.algo')]);
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
        if(! $application = $this->applicationModel->where('name',$payload->iss)->first()){
            return false;
        }
        if(! $result =  $this->jws->isValid($application->secret,$header->alg)){
            return false;
        }
        $this->application = $application;
        $userData = explode('|', $payload->sub);
        $this->userId = $userData[0];
        if(!isset($userData[1]))
            $userData[1] ='';
        $this->userPermissions = explode(',', $userData[1]);
        return true;
    }

    public function forApplication($application, $userId = null, $permissions = [])
    {
        $sub = "";
        if($userId) {

            if (is_array($permissions) && count($permissions))
                $permissions = '|' . implode(',', $permissions);
            else
                $permissions = '';
            $sub = "$userId$permissions";
        }
        $this->jws->setPayload(['iss' => $application->name, 'sub' => $sub, 'iat' => time()]);
        $this->jws->sign($application->secret);
        return $this->jws->getTokenString();
    }
}
