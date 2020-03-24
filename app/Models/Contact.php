<?php

namespace App\Models;

class Contact extends Model
{
    public function contactable()
    {
        return $this->morphTo();
    }
}
