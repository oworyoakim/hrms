<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

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

}
