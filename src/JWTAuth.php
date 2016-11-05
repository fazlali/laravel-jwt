<?php

namespace Fazlali\LaravelJWT;
//use Fazlali\LaravelJWT\JWS;
//use Illuminate\Http\Request;

class JWTAuth
{
    protected $application;
    protected $user;
    protected $jws;
    protected $request;
    protected $applicationModel;
    protected $auth;
    protected $appName;
    protected $secret;

    public function __construct( $request, $auth)
    {
        $this->userModel = app(config('jwt.models.user'));
        $this->auth = $auth;
        $this->appName = config('jwt.auth.app_name');
        $this->secret = config('jwt.auth.secret');
        $this->jws = new JWS(['alg' => config('jwt.algo')]);
        $this->setRequest($request);
    }


    public function user(){
        if($this->check())
            return $this->user;
        return null;
    }


    // extract token from request

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


    //check if token is valid and extract user

    public function check(){

        if(! $token = $this->token())
            return false;
        if(count($token_parts = explode('.', $token)) !== 3){
            return false;
        }
        $header = json_decode(base64_decode($token_parts[0]));
        $payload = json_decode(base64_decode($token_parts[1]));
        if($this->appName != $payload->iss){
            return false;
        }
        if(! $result =  $this->jws->isValid($this->secret,$header->alg)){
            return false;
        }
        if(! $this->user = $this->userModel->find(explode('|',$payload->sub)[0]))
            return false;
        if( $this->user->jwtValidSince && ($validSince = $this->user->{$this->user->jwtValidSince}() )){
            if((isset($payload->iat) ? $payload->iat : 0) < (isset($validSince) ? $validSince->timestamp : 0) ){
                $this->user = null;
                return false;
            }
        }
        return true;
    }



    // attempt to login by jwt and return token for user

    public function attempt(array $credentials = [])
    {
        if (! $this->auth->once($credentials)) {
            return false;
        }

        return $this->fromUser($this->auth->user());
    }


    // return token for given user

    public function fromUser($user = null){

        $user = $user ?: $this->auth->user();
        $sub = "";
        if($user) {
            $permissions = $user->{$user->permissions}();

            if (count($permissions))
                $permissions = '|' . implode(',', $permissions);
            else
                $permissions = '';
            $sub = "$user->id$permissions";
        }
        $this->jws->setPayload(['iss' => $this->appName, 'sub' => $sub, 'iat' => time()]);
        $this->jws->sign($this->secret);
        return $this->jws->getTokenString();
    }
}
