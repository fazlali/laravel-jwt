<?php

namespace Fazlali\LaravelJWT\Facades;

use Illuminate\Support\Facades\Facade;

class JWTAPI extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'fazlali.jwt.api';
    }
}
