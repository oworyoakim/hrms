<?php

namespace App\Models;

class EmploymentHistory extends Model
{
    protected $table = 'employment_history';

    protected $dates = ['start_date','end_date'];

    public function employee(){
        return $this->belongsTo(Employee::class,'employee_id');
    }
}
