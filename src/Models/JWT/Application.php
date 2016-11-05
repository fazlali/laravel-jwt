<?php

namespace App\JWT;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    public function setOriginsAttribute($value)
    {
        $value = collect($value)->filter(function ($item){
            return $item;
        });
        return $this->attributes['origins'] = $value->toJson();

    }

    public function getOriginsAttribute($value)
    {
        $value = $value ?:"[]";
        return json_decode($value);
    }
}
