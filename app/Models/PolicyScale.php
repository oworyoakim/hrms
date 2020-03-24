<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class PolicyScale extends Model
{
    public function policy(){
        return $this->belongsTo(LeavePolicy::class,'leave_policy_id');
    }

    public function scale(){
        return $this->belongsTo(SalaryScale::class,'salary_scale_id');
    }

    public function scopeActive(Builder $query){
        return $query->where('active',true);
    }
}
