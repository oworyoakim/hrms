<?php

namespace App\Models;

class LeaveBalance extends Model
{
    protected $dates = ['join_date','start_date','end_date'];

    public function employee(){
        return $this->belongsTo(Employee::class,'employee_id');
    }

    public function leaveType(){
        return $this->belongsTo(LeaveType::class,'leave_type_id');
    }

    public function policy(){
        return $this->belongsTo(LeavePolicy::class,'policy_id');
    }
}
