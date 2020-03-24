<?php

namespace App\Models;

use App\Traits\Commentable;
use Illuminate\Database\Eloquent\Builder;

class LeaveApplication extends Model
{
    use Commentable;
    protected $table = 'leave_applications';
    protected $dates = ['start_date','end_date', 'deleted_at'];
    protected $with = ['leaveType'];

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function scopePending(Builder $query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeVerified(Builder $query)
    {
        return $query->where('status', 'verified');
    }

    public function scopeReturned(Builder $query)
    {
        return $query->where('status', 'returned');
    }

    public function scopeApproved(Builder $query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeDeclined(Builder $query)
    {
        return $query->where('status', 'declined');
    }

    public function scopeGranted(Builder $query)
    {
        return $query->where('status', 'granted');
    }

    public function scopeRejected(Builder $query)
    {
        return $query->where('status', 'rejected');
    }

}
