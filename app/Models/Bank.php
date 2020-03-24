<?php

namespace App\Models;

class Bank extends Model
{
    protected $table = 'banks';

    public function employee(){
        return $this->belongsTo(Employee::class,'employee_id');
    }
}
