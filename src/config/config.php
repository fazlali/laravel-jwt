<?php

return [
    /*
    |--------------------------------------------------------------------------
    | JWT time to live
    |--------------------------------------------------------------------------
    |
    | Specify the length of time (in minutes) that the token will be valid for.
    | Defaults to 1 hour
    |
    */

    'ttl' => 60,

    /*
    |--------------------------------------------------------------------------
    | Refresh time to live
    |--------------------------------------------------------------------------
    |
    | Specify the length of time (in minutes) that the token can be refreshed
    | within. I.E. The user can refresh their token within a 2 week window of
    | the original token being created until they must re-authenticate.
    | Defaults to 2 weeks
    |
    */

    'refresh_ttl' => 20160,

    /*
    |--------------------------------------------------------------------------
    | JWT hashing algorithm
    |--------------------------------------------------------------------------
    |
    | Specify the hashing algorithm that will be used to sign the token.
    |
    | See here: https://github.com/namshi/jose/tree/2.2.0/src/Namshi/JOSE/Signer
    | for possible values
    |
    */

    'algo' => 'HS256',

    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    |
    | Specify the various eloquent models used throughout the package.
    |
    */

    'models' => [

        /*
        |--------------------------------------------------------------------------
        | User Model
        |--------------------------------------------------------------------------
        |
        | Specify the  eloquent model that is used to find the user based
        | on the subject claim
        |
        */

        'user' => 'App\User',

    ],

    /*
    |--------------------------------------------------------------------------
    | JWT Authentication Secret
    |--------------------------------------------------------------------------
    |
    | Don't forget to set this, as it will be used to sign your tokens.
    | You should get this code from api server
    |
    */


    'api' => [
        /*
        |--------------------------------------------------------------------------
        | JWT Application Name
        |--------------------------------------------------------------------------
        |
        | Registered name for your application in the api server to use ass JWT issuer
        | You should get this name from the api server
        |
        */

//        'application_name' => env('JWT_APPLICATION_NAME', 'changeme'),


        /*
         |--------------------------------------------------------------------------
         | Models
         |--------------------------------------------------------------------------
         |
         | Specify the various eloquent models used throughout the package.
         |
         */

        'models' => [



            /*
            |--------------------------------------------------------------------------
            | Application Model
            |--------------------------------------------------------------------------
            |
            | Specify the eloquent model that is used to find the application based
            | on the issuer claim
            |
            */
            'application' => 'App\JWT\Application',

        ],
    ],

    'auth' => [

        /*
        |--------------------------------------------------------------------------
        | JWT Authentication Secret
        |--------------------------------------------------------------------------
        |
        | Don't forget to set this, as it will be used to sign your tokens.
        | You should get this code from api server
        |
        */

        'secret' => env('JWT_SECRET', 'changeme'),

        /*
        |--------------------------------------------------------------------------
        | Authentication Facades
        |--------------------------------------------------------------------------
        |
        |
        */

//        'auth' => 'Illuminate\Support\Facades\Auth',

        /*
        |--------------------------------------------------------------------------
        | Application name
        |--------------------------------------------------------------------------
        |
        | You should get this code from api server
        |
        */

        'app_name' => env('JWT_APP_NAME', 'video'),
    ]

];
