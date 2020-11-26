<?php

namespace App\Models;

use App\Traits\Commentable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use stdClass;


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

    public function getDetails()
    {
        $application = new stdClass();
        $application->id = $this->id;
        $application->duration = $this->duration;
        $application->startDate = $this->start_date->toDateString();
        $application->endDate = $this->end_date->toDateString();
        $application->status = $this->status;
        $application->createdAt = $this->created_at->toDateTimeString();
        $application->updatedAt = $this->updated_at->toDateTimeString();
        $application->deletedAt = ($this->deleted_at) ? $this->deleted_at->toDateTimeString() : null;
        //dd($application);
        $application->employee = null;
        if ($this->employee)
        {
            $application->employee = new stdClass();
            $application->employee->id = $this->employee->id;
            $application->employee->name = $this->employee->fullName();
            $application->employee->designationId = $this->employee->designation_id;
        }
        $application->leaveType = null;
        if ($this->leaveType)
        {
            $application->leaveType = new stdClass();
            $application->leaveType->id = $this->leaveType->id;
            $application->leaveType->title = $this->leaveType->title;
        }

        $application->nextActor = null;

        if (in_array($this->status, ['pending', 'verified', 'approved']) && $application->employee && $designationId = $this->employee->designation_id)
        {
            if ($applicationSetting = LeaveApplicationSetting::query()->where('designation_id', $designationId)->first())
            {
                switch ($this->status)
                {
                    case 'pending':
                        if ($applicationSetting->verified_by && $designation = Designation::query()->find($applicationSetting->verified_by))
                        {
                            $application->nextActor = $designation->getHolders()->first();
                        }
                        break;
                    case 'verified':
                        if ($applicationSetting->approved_by && $designation = Designation::query()->find($applicationSetting->approved_by))
                        {
                            $application->nextActor = $designation->getHolders()->first();
                        }
                        break;
                    case 'approved':
                        if ($applicationSetting->granted_by && $designation = Designation::query()->find($applicationSetting->granted_by))
                        {
                            $application->nextActor = $designation->getHolders()->first();
                        }
                        break;
                    default:
                        break;
                }
            }
        }

        /*
         * A leave application can be verified if:
         * 1. Its pending
         * 2. It's start date is in the future
         * 3. The current logged in employee's designation is the set verifier for the applicant's designation
         * 4. The logged in employee has permissions to verify leave applications
         */
        $application->canBeVerified = $this->status == 'pending' && $this->start_date->greaterThan(Carbon::today());
        $application->canBeReturned = $this->status == 'pending' && $this->start_date->greaterThan(Carbon::today());
        $application->canBeApproved = $this->status == 'verified' && $this->start_date->greaterThan(Carbon::today());
        $application->canBeDeclined = $this->status == 'verified' && $this->start_date->greaterThan(Carbon::today());
        $application->canBeGranted = $this->status == 'approved' && $this->start_date->greaterThan(Carbon::today());
        $application->canBeRejected = $this->status == 'approved' && $this->start_date->greaterThan(Carbon::today());
        $application->canBeDeleted = false;
        $application->canBeEdited = false;

        return $application;
    }

}
