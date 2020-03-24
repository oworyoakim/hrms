<?php

namespace App\Models;

class SalaryScale extends Model
{
    protected $table = 'salary_scales';

    public function policies()
    {
        return $this->belongsToMany(LeavePolicy::class, 'policy_scales', 'salary_scale_id', 'leave_policy_id')
                    ->using(PolicyScale::class)
                    ->withTimestamps();
    }

}
