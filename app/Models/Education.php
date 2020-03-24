<?php

namespace App\Models;


class Education extends Model
{
    protected $table = 'educations';

    public function employee(){
        return $this->belongsTo(Employee::class,'employee_id');
    }
}
