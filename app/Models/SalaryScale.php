<?php

namespace App\Models;

use stdClass;

class SalaryScale extends Model
{
    protected $table = 'salary_scales';

    public function policies()
    {
        return $this->belongsToMany(LeavePolicy::class, 'policy_scales', 'salary_scale_id', 'leave_policy_id')
                    ->using(PolicyScale::class)
                    ->withTimestamps();
    }

    public function getDetails(){
        $salaryScale = new stdClass();
        $salaryScale->id = $this->id;
        $salaryScale->scale = $this->scale;
        $salaryScale->rank = $this->rank;
        $salaryScale->description = $this->description;
        $salaryScale->createdBy = $this->created_by;
        $salaryScale->updatedBy = $this->updated_by;
        $salaryScale->createdAt = $this->created_at->toDateString();
        $salaryScale->updatedAt = $this->updated_at->toDateString();

        return $salaryScale;
    }

}
