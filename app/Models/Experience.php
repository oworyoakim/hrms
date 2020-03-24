<?php

namespace App\Models;

class Experience extends Model
{
    protected $table = 'experiences';

    public function employee(){
        return $this->belongsTo(Employee::class,'employee_id');
    }
}
