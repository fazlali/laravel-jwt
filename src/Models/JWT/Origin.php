<?php

namespace App\JWT;

use Illuminate\Database\Eloquent\Model;

class Origin extends Model
{
    public $timestamps = false;

    public function application()
    {
        return $this->belongsTo('App\JWT\Application');
    }

}
