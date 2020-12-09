<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use stdClass;

class LeaveType extends Model
{
    use SoftDeletes;
    protected $table = 'leave_types';

    public function policies(){
        return $this->hasMany(LeavePolicy::class,'leave_type_id');
    }

    public function leaves(){
        return $this->hasMany(Leave::class,'leave_type_id');
    }

    public function scopeActive(Builder $query)
    {
        return $query->where('active', true);
    }

    public function scopeInactive(Builder $query)
    {
        return $query->where('active', false);
    }

    public function getDetails(){
        $leaveType = new stdClass();
        $leaveType->id = $this->id;
        $leaveType->title = $this->title;
        $leaveType->description = $this->description;
        $leaveType->active = !!$this->active;
        $leaveType->earnedLeave = !!$this->earned_leave;
        $leaveType->numOngoing = $this->leaves()->ongoing()->count();
        return $leaveType;
    }

}
