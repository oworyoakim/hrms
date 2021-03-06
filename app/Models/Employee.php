<?php

namespace App\Models;

use App\Traits\Addressable;
use App\Traits\BelongsToDirectorate;
use App\Traits\BelongsToExecutiveDirector;
use App\Traits\Commentable;
use App\Traits\Contactable;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use stdClass;

/**
 * Class Employee
 *
 * @author yoakim
 * @property int id
 * @property int user_id
 * @property string employee_number
 * @property string username
 * @property string title
 * @property string first_name
 * @property string last_name
 * @property string middle_name
 * @property string other_names
 * @property string gender
 * @property string email
 * @property string nin
 * @property string passport
 * @property string nssf
 * @property string tin
 * @property string permit
 * @property bool approved
 * @property string nationality
 * @property string employee_status
 * @property string employment_term
 * @property string employment_status
 * @property string employment_type
 * @property int created_by
 * @property int approved_by
 * @property int salary_scale_id
 * @property int designation_id
 * @property int section_id
 * @property int division_id
 * @property int department_id
 * @property int directorate_id
 * @property string $marital_status
 * @property string religion
 * @property string avatar
 * @property Carbon dob
 * @property Carbon approved_at
 * @property Carbon date_joined
 * @property Carbon exit_date
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 *
 */
class Employee extends Model
{
    use Addressable, Contactable, Commentable, BelongsToExecutiveDirector, BelongsToDirectorate;

    const STATUS_ONBOARDING = 'onboarding';
    const STATUS_ACTIVE = 'active';
    const STATUS_ONLEAVE = 'onleave';
    const STATUS_SUSPENDED = 'suspended';
    const STATUS_RESIGNED = 'resigned';
    const STATUS_DEAD = 'dead';
    const STATUS_RETIRED = 'retired';
    const STATUS_DISMISSED = 'dismissed';
    const STATUS_SECONDED = 'seconded';
    const STATUS_RELEASED = 'released';

    protected $dates = [
        'dob',
        'date_joined',
        'exit_date',
        'approved_at'
    ];

    public function fullName()
    {
        $fullName = "{$this->title} {$this->first_name} {$this->last_name}";
        if (!empty($this->other_names))
        {
            $fullName .= " {$this->other_names}";
        }
        if (!empty($this->middle_name))
        {
            $fullName .= " {$this->middle_name}";
        }
        return $fullName;
    }

    /**
     * Return true if the employee is allowed to log into the system
     * @return bool
     */
    public function canLogin()
    {
        return in_array($this->employee_status, [Employee::STATUS_ACTIVE, Employee::STATUS_ONLEAVE]);
    }

    public function scale()
    {
        return $this->belongsTo(SalaryScale::class, 'salary_scale_id');
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }

    public function directorate()
    {
        return $this->belongsTo(Directorate::class, 'directorate_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function leaveApplications()
    {
        return $this->hasMany(LeaveApplication::class, 'employee_id');
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class, 'employee_id');
    }

    public function leaveBalances()
    {
        return $this->hasMany(LeaveBalance::class, 'employee_id');
    }

    public function histories()
    {
        return $this->hasMany(EmploymentHistory::class, 'employee_id');
    }

    public function relatedPersons()
    {
        return $this->hasMany(RelatedPerson::class, 'employee_id');
    }

    public function educations()
    {
        return $this->hasMany(Education::class, 'employee_id');
    }

    public function experiences()
    {
        return $this->hasMany(Experience::class, 'employee_id');
    }

    public function banks()
    {
        return $this->hasMany(Bank::class, 'employee_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'employee_id');
    }

    public function active(Builder $query)
    {
        return $query->where('employee_status', Employee::STATUS_ACTIVE);
    }

    public function suspended(Builder $query)
    {
        return $query->where('employee_status', Employee::STATUS_SUSPENDED);
    }

    public function onleave(Builder $query)
    {
        return $query->where('employee_status', Employee::STATUS_ONLEAVE);
    }

    public function onboard(Builder $query)
    {
        return $query->where('employee_status', Employee::STATUS_ONBOARDING);
    }

    public function exited(Builder $query)
    {
        return $query->whereIn('employee_status', [
            Employee::STATUS_RESIGNED,
            Employee::STATUS_DISMISSED,
            Employee::STATUS_DEAD,
            Employee::STATUS_RETIRED,
            Employee::STATUS_SECONDED,
        ]);
    }

    public function subordinates()
    {
        $subordinates = Collection::make();
        if ($this->designation)
        {
            foreach ($this->designation->subordinates as $designation)
            {
                foreach ($designation->getHolders() as $subordinate)
                {
                    $subordinates->push($subordinate);
                }
            }
        }
        return $subordinates;
    }


    public function supervisor()
    {
        if (!$this->designation || !$this->designation->supervisor)
        {
            return null;
        }
        return $this->designation->supervisor->getHolders()->first();
    }

    public function getDetails($expended = true)
    {
        $employee = new stdClass();
        $employee->id = $this->id;
        $employee->userId = $this->user_id;
        $employee->title = $this->title;
        $employee->firstName = $this->first_name;
        $employee->lastName = $this->last_name;
        $employee->middleName = $this->middle_name;
        $employee->otherNames = $this->other_names;
        $employee->fullName = $this->fullName();
        $employee->employeeNumber = $this->employee_number;
        $employee->email = $this->email;
        $employee->username = $this->username;
        $employee->gender = $this->gender;
        $employee->dob = $this->dob ? $this->dob->toDateString() : null;
        $employee->joinDate = $this->date_joined ? $this->date_joined->toDateString() : null;
        $employee->exitDate = $this->exit_date ? $this->exit_date->toDateString() : null;
        $employee->avatar = $this->avatar;
        $employee->nin = $this->nin;
        $employee->passport = $this->passport;
        $employee->permit = $this->permit;
        $employee->tin = $this->tin;
        $employee->nssf = $this->nssf;
        $employee->approved = !!$this->approved;
        $employee->nationality = $this->nationality;
        $employee->approved = !!$this->approved;
        $employee->employeeStatus = $this->employee_status;
        $employee->employmentStatus = $this->employment_status;
        $employee->employmentTerm = $this->employment_term;
        $employee->employmentType = $this->employment_type;
        $employee->maritalStatus = $this->marital_status;
        $employee->religion = $this->religion;
        $employee->directorateId = $this->directorate_id ?: null;
        $employee->departmentId = $this->department_id ?: null;
        $employee->divisionId = $this->division_id ?: null;
        $employee->sectionId = $this->section_id ?: null;
        $employee->designationId = $this->designation_id ?: null;
        $employee->salaryScaleId = $this->salary_scale_id ?: null;
        $employee->contacts = $this->contacts()->get()->map(function (Contact $contact) {
            return $contact->getDetails();
        });
        $employee->createdBy = $this->created_by;
        $employee->updatedBy = $this->updated_by;
        $employee->approvedBy = $this->approved_by;
        $employee->createdAt = $this->created_at->toDateString();
        $employee->updatedAt = $this->updated_at->toDateString();
        $employee->approvedAt = $this->approved_at ? $this->approved_at->toDateString() : null;
        $employee->designation = null;
        $employee->salaryScale = null;
        $employee->nextWorkAnniversary = null;
        $employee->nextOfKin = null;
        $employee->supervisor = null;
        $employee->subordinates = [];
        $employee->directorate = null;
        $employee->department = null;
        $employee->division = null;
        $employee->section = null;
        // Leave application settings for this employee
        $employee->leaveApplicationVerifierId = null;
        $employee->leaveApplicationApproverId = null;
        $employee->leaveApplicationGrantorId = null;
        $leaveApplicationSetting = LeaveApplicationSetting::query()->where('designation_id', $this->designation_id)->first();
        if ($leaveApplicationSetting)
        {
            $employee->leaveApplicationVerifierId = $leaveApplicationSetting->verified_by;
            $employee->leaveApplicationApproverId = $leaveApplicationSetting->approved_by;
            $employee->leaveApplicationGrantorId = $leaveApplicationSetting->granted_by;
        }
        $employee->verifiableLeaveApplicants = LeaveApplicationSetting::query()->where('verified_by', $this->designation_id)->pluck('designation_id')->all();
        $employee->approvableLeaveApplicants = LeaveApplicationSetting::query()->where('approved_by', $this->designation_id)->pluck('designation_id')->all();
        $employee->grantableLeaveApplicants = LeaveApplicationSetting::query()->where('granted_by', $this->designation_id)->pluck('designation_id')->all();

        if ($expended)
        {
            $employee->supervisor = $this->supervisor();

            $employee->subordinates = $this->subordinates();

            $employee->directorate = $this->directorate ? $this->directorate->getDetails() : null;

            $employee->department = $this->department ? $this->department->getDetails() : null;

            $employee->division = $this->division ? $this->division->getDetails() : null;

            $employee->section = $this->section ? $this->section->getDetails() : null;

            if ($this->designation)
            {
                $employee->designation = new stdClass();
                $employee->designation->id = $this->designation->id;
                $employee->designation->title = $this->designation->title;
            }

            $employee->salaryScale = $this->scale ? $this->scale->getDetails() : null;
            $nextWorkAnniversary = $this->nextWorkAnniversary();
            $employee->nextWorkAnniversary = $nextWorkAnniversary ? $nextWorkAnniversary->toDateString() : null;
            $nextOfKin = $this->relatedPersons()->where('is_next_of_kin', true)->first();
            $employee->nextOfKin = !empty($nextOfKin) ? $nextOfKin->getDetails() : null;
        }
        return $employee;
    }

    /**
     * Checks if an employee is allowed to apply for the given leave
     *
     * @param LeaveType $leaveType
     * @param Carbon $startDate
     * @param int $duration
     *
     * @return bool
     * @throws Exception
     */
    public function checkIfCanApplyFor(LeaveType $leaveType, Carbon $startDate, int $duration)
    {
        // check for any applications in processing stages for this leave
        $applications = $this->leaveApplications()
                             ->where('leave_type_id', $leaveType->id)
                             ->whereIn('status', [
                                 'pending',
                                 'verified',
                                 'approved'
                             ]);
        if ($applications->count())
        {
            throw new Exception('You have an application in process for this leave!');
        }
        $endDate = $startDate->clone()->addDays($duration);
        // for any other leave type, check for any applications in processing stages whose dates overlap with current request
        $applications = $this->leaveApplications()
                             ->whereNotIn('leave_type_id', [
                                 $leaveType->id
                             ])
                             ->whereIn('status', [
                                 'pending',
                                 'verified',
                                 'approved'
                             ])
                             ->where(function ($query) use ($startDate, $endDate) {
                                 $query->where(function ($sql) use ($startDate) {
                                     $sql->whereDate('start_date', '>=', $startDate->format('Y-m-d'))
                                         ->whereDate('end_date', '<=', $startDate->format('Y-m-d'));
                                 })
                                       ->orWhere(function ($sql) use ($endDate) {
                                           $sql->whereDate('start_date', '>=', $endDate->format('Y-m-d'))
                                               ->whereDate('end_date', '<=', $endDate->format('Y-m-d'));
                                       });
                                 // $query->whereRaw("{$startDate->format('Y-m-d')} BETWEEN DATE(start_date) AND DATE(end_date)")
                                 // ->orWhereRaw("{$endDate->format('Y-m-d')} BETWEEN DATE(start_date) AND DATE(end_date)");
                             });
        if ($applications->count())
        {
            throw new Exception('You have pending applications with similar dates!');
        }

        // if no designation attached to employee
        if (!$this->designation)
        {
            throw new Exception('You have no designation!');
        }
        $salaryScaleId = $this->salary_scale_id;
        if (!$salaryScaleId)
        {
            throw new Exception('Your have no salary scale!');
        }

        // Check for overlapping leave days
        $lastAnniversary = $this->lastWorkAnniversary();
        $datesOnLeave = LeaveTracker::query()->whereDate('date_on_leave', '>=', $lastAnniversary)
                                    ->where('status', 'onleave')
                                    ->where('employee_id', $this->id)
                                    ->pluck('date_on_leave')
                                    ->map(function (Carbon $date) {
                                        return $date->format('Y-m-d');
                                    })
                                    ->all();

        $period = CarbonPeriod::create($startDate, $endDate);
        $datesRequested = array_map(function (CarbonInterface $date) {
            return $date->format('Y-m-d');
        }, $period->toArray());
        $intersection = array_intersect($datesOnLeave, $datesRequested);
        if (count($intersection))
        {
            throw new Exception('Sorry, you are still on leave!');
        }
        // check if there are no days left for the leave if its earned leave
        $activePolicy = $this->getActiveLeavePolicy($leaveType->id);
        // If this leave has no policy, reject request
        if (!$activePolicy)
        {
            throw new Exception("Sorry, you cannot apply for this leave because there is no active policy associated with this leave. Contact HR for help!");
        }

        $numEntries = PolicyScale::active()->where('leave_policy_id', $activePolicy->id)
                                 ->where('salary_scale_id', $salaryScaleId)
                                 ->count();

        if (!$numEntries)
        {
            throw new Exception("Sorry, you cannot apply for this leave because there is no active policy associated with this leave. Contact HR for help!");
        }
        // check for remaining leave days for earned leave
        if ($leaveType->earned_leave)
        {
            $totalAvailableDays = $this->getTotalRemainingDaysForEarnedLeave($leaveType->id, $startDate);
            if ($totalAvailableDays < $duration)
            {
                throw new Exception("Sorry, the maximum days you can apply for this leave is {$totalAvailableDays}");
            }
        }
        return true;
    }

    /**
     *
     * @return Carbon|\Carbon\Carbon|null
     */
    public function lastWorkAnniversary()
    {
        $today = Carbon::today();
        if (!$this->date_joined || $this->date_joined->greaterThan($today))
        {
            return null;
        }
        $date = $this->date_joined->clone();
        $years = $date->diffInYears($today);
        return $date->addYears($years);
    }

    /**
     *
     * @return Carbon|\Carbon\Carbon|null
     */
    public function nextWorkAnniversary()
    {
        $lastWorkAnniversary = $this->lastWorkAnniversary();

        if (!$lastWorkAnniversary)
        {
            return null;
        }
        return $lastWorkAnniversary->addYears(1);
    }

    /**
     *
     * @return \Carbon\Carbon|null
     */
    public function retirementDate()
    {
        if (!$this->dob)
        {
            return null;
        }
        $retirementAge = 60;

        return $this->dob->clone()->addYears($retirementAge);
    }

    /**
     *
     * @param int $leaveTypeId
     */
    public function loadLeaveBalances($leaveTypeId)
    {
        // there are no active policy for this leave type
        $activePolicies = LeavePolicy::active()->where('leave_type_id', $leaveTypeId)->get();
        if (!$activePolicies->count())
        {
            return;
        }
        // employee already has leave balance entry for this leave type
        if ($this->leaveBalances()
                 ->where('leave_type_id', $leaveTypeId)
                 ->count())
        {
            return;
        }

        foreach ($activePolicies as $policy)
        {
            $end = Carbon::today()->endOfCentury();
            $start = $this->date_joined;
            while ($start->lessThanOrEqualTo($end))
            {
                $clone = $start->clone()->addMonths(12);
                $this->leaveBalances()->create([
                    'leave_type_id' => $leaveTypeId,
                    'policy_id' => $policy->id,
                    'join_date' => $this->date_joined,
                    'start_date' => $start->clone(),
                    'end_date' => $clone->subDays(1),
                    'entitlement' => $policy->duration,
                    'balance' => $policy->duration
                ]);
                $start->addMonths(12);
            }
        }
    }

    public function redesignate()
    {

    }

    public function demote()
    {

    }

    public function promote()
    {

    }

    public function suspend()
    {

    }

    public function retire()
    {

    }

    public function dismiss()
    {

    }

    /**
     * Resigns an employee at midnight
     *
     * @param Carbon $date
     * @param $comment
     */
    public function resign(Carbon $date, $comments)
    {
        if ($date->toDateString() == Carbon::today()->toDateString())
        {
            $this->histories()->create([
                'action' => EmploymentHistory::ACTION_RESIGN,
                'start_date' => $date,
                'from_designation_id' => $this->designation_id,
                'comments' => $comments,
            ]);

            $this->employment_status = Employee::STATUS_RESIGNED;

            $this->exit_date = $date;

            $this->save();
        }
    }

    public function getActiveLeavePolicy(int $leaveTypeId)
    {
        $policyBuilder = LeavePolicy::active();
        $policyBuilder->where('leave_type_id', $leaveTypeId);
        if ($this->gender == 'male' || $this->gender == 'female')
        {
            $policyBuilder->where(function ($query) {
                $query->where('gender', $this->gender)
                      ->orWhere('gender', 'both');
            });
        } else
        {
            $policyBuilder->where('gender', $this->gender);
        }
        $activePolicy = $policyBuilder->first();

        return $activePolicy;
    }

    public function getEntitlement(int $leaveTypeId)
    {
        $activePolicy = $this->getActiveLeavePolicy($leaveTypeId);
        if (empty($activePolicy))
        {
            return 0;
        }
        return $activePolicy->duration;
    }

    public function getTotalDaysEarned(int $leaveTypeId)
    {
        $lastAnniversary = $this->lastWorkAnniversary();
        if (empty($lastAnniversary))
        {
            return 0;
        }
        $entitlement = $this->getEntitlement($leaveTypeId);
        $totalDaysWorked = Carbon::today()->diffInDays($lastAnniversary);
        $daysEarned = round(($totalDaysWorked / 365) * $entitlement);

        return $daysEarned;
    }

    public function getTotalRemainingDaysForEarnedLeave($leaveTypeId, Carbon $startDate)
    {
        // get the total days earned for earned leave
        $daysEarned = $this->getTotalDaysEarned($leaveTypeId);
        // get the days spent on leave
        $spent = LeaveTracker::query()->where('employee_id', $this->id)
                             ->where('leave_type_id', $leaveTypeId)
                             ->where('status', 'onleave')
                             ->whereDate("period_start_date", '<=', $startDate->toDateString())
                             ->whereDate('period_end_date', '>=', $startDate->toDateString())
                             ->count();
        // get the balances
        $balances = $this->getTotalLeaveBalances($leaveTypeId, $startDate);
        // get the total remain days
        $totalAvailableDays = $daysEarned + $balances - $spent;

        return $totalAvailableDays;
    }

    public function getTotalLeaveBalances(int $leaveTypeId, Carbon $endDate){
        if(!$this->date_joined){
            return 0;
        }
        return $this->leaveBalances()
                    ->where('leave_type_id', $leaveTypeId)
                    ->whereDate('start_date', '>=', $this->date_joined)
                    ->whereDate('end_date', '<=', $endDate)
                    ->sum('balance');
    }


}
