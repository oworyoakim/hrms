<?php

namespace App\Models;

use App\Traits\Commentable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Leave extends Model
{
    use SoftDeletes, Commentable;

    protected $guarded = [];
    protected $dates = ['start_date', 'end_date'];
    protected $with = ['leaveType'];

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }

    public function leavePolicy()
    {
        return $this->belongsTo(LeavePolicy::class, 'policy_id');
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function completionUser()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function scopePending(Builder $builder)
    {
        return $builder->where('status', 'pending');
    }

    public function scopeOngoing(Builder $builder)
    {
        return $builder->where('status', 'ongoing');
    }

    public function scopeCompleted(Builder $builder)
    {
        return $builder->where('status', 'completed');
    }

    public function scopeRecalled(Builder $builder)
    {
        return $builder->where('status', 'recalled');
    }

    public function scopeCanceled(Builder $builder)
    {
        return $builder->where('status', 'canceled');
    }
}
