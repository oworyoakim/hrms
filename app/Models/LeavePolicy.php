<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use stdClass;

class LeavePolicy extends Model
{
    protected $table = 'leave_policies';
    protected $with = ['leaveType'];


    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }

    public function scales()
    {
        return $this->hasMany(PolicyScale::class, 'leave_policy_id');
    }

    public function scopeActive(Builder $query)
    {
        return $query->where('active', true);
    }

    public function scopeInactive(Builder $query)
    {
        return $query->where('active', false);
    }

    public function scopeMale(Builder $query)
    {
        return $query->where('gender', 'male');
    }

    public function scopeFemale(Builder $query)
    {
        return $query->where('gender', 'female');
    }

    public function scopeBoth(Builder $query)
    {
        return $query->whereNull('gender');
    }

    /**
     * @param array $scaleIds
     * @return int
     */
    public function addPolicyScales(array $scaleIds)
    {
        $count = 0;
        foreach ($scaleIds as $scaleId)
        {
            $this->scales()->save(new PolicyScale([
                'salary_scale_id' => $scaleId,
                'active' => $this->active,
            ]));
            $count++;
        }
        return $count;
    }

    public function getDetails(){
        $policy = new stdClass();
        $policy->id = $this->id;
        $policy->title = $this->title;
        $policy->description = $this->description;
        $policy->gender = $this->gender;
        $policy->leaveTypeId = $this->leave_type_id;
        $policy->leaveType = $this->leaveType;
        $policy->withWeekend = !!$this->with_weekend;
        $policy->earnedLeave = !!$this->earned_leave;
        $policy->carryForward = !!$this->carry_forward;
        $policy->duration = $this->duration;
        $policy->maxCarryForwardDuration = $this->max_carry_forward_duration;
        $policy->active = !!$this->active;
        $policy->salaryScaleIds = $this->scales()->pluck('salary_scale_id')->all();
        $policy->salaryScales = $this->scales()
                                     ->get()
                                     ->transform(function ($policyScale) {
                                         $salaryScale = new stdClass();
                                         $salaryScale->id = $policyScale->salary_scale_id;
                                         $salaryScale->scale = $policyScale->scale->scale;
                                         $salaryScale->rank = $policyScale->scale->rank;
                                         $salaryScale->description = $policyScale->scale->description;
                                         $salaryScale->active = !!$policyScale->active;
                                         return $salaryScale;
                                     });
        return $policy;
    }

}
