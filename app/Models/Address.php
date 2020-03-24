<?php

namespace App\Models;

class Address extends Model
{
    public function addressable()
    {
        return $this->morphTo();
    }
}
