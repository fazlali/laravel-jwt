<?php

namespace App\JWT;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function videos (){
        return $this->hasMany('App\Video');
    }

    public function contents (){
        return $this->hasMany('App\content');
    }

    public function origins (){
        return $this->hasMany('App\JWT\Origin');
    }


}
