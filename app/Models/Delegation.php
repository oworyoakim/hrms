<?php

namespace App\Models;

class Delegation extends Model
{
    protected $table = 'delegations';

    public function delegator()
    {
        return $this->hasMany(Designation::class, 'substantive_id');
    }

    public function delegate()
    {
        return $this->hasMany(Designation::class, 'delegated_id');
    }
}
