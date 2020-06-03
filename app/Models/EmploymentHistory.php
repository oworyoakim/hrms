<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class EmploymentHistory extends Model
{
    protected $table = 'employment_history';

    protected $dates = ['start_date', 'end_date'];

    const ACTION_APPOINT = 'appointment';
    const ACTION_SUSPEND = 'suspension';
    const ACTION_DISMISS = 'dismissal';
    const ACTION_RETIRE = 'retirement';
    const ACTION_RESIGN = 'resignation';
    const ACTION_DELEGATE = 'delegation';
    const ACTION_DEMOTE = 'demotion';
    const ACTION_PROMOTE = 'promotion';
    const ACTION_TERMINATE = 'termination';
    const ACTION_DEATH = 'died';
    const ACTION_SECOND = 'secondment';
    const ACTION_RELEASE = 'release';

    const ACTIONS = [
        EmploymentHistory::ACTION_APPOINT,
        EmploymentHistory::ACTION_SUSPEND,
        EmploymentHistory::ACTION_DISMISS,
        EmploymentHistory::ACTION_RETIRE,
        EmploymentHistory::ACTION_RESIGN,
        EmploymentHistory::ACTION_DELEGATE,
        EmploymentHistory::ACTION_DEMOTE,
        EmploymentHistory::ACTION_PROMOTE,
        EmploymentHistory::ACTION_TERMINATE,
        EmploymentHistory::ACTION_DEATH,
        EmploymentHistory::ACTION_SECOND,
        EmploymentHistory::ACTION_RELEASE,
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function exits(Builder $query){
        return $query->whereIn('action',[
            EmploymentHistory::ACTION_RESIGN,
            EmploymentHistory::ACTION_DISMISS,
            EmploymentHistory::ACTION_DEATH,
            EmploymentHistory::ACTION_RETIRE,
            EmploymentHistory::ACTION_SECOND,
            EmploymentHistory::ACTION_RELEASE,
        ]);
    }

    public function appointments(Builder $query){
        return $query->where('action',EmploymentHistory::ACTION_APPOINT);
    }

    public function promotions(Builder $query){
        return $query->where('action',EmploymentHistory::ACTION_PROMOTE);
    }

    public function demotions(Builder $query){
        return $query->where('action',EmploymentHistory::ACTION_DEMOTE);
    }

    public function delegations(Builder $query){
        return $query->where('action',EmploymentHistory::ACTION_DELEGATE);
    }

    public function retirements(Builder $query){
        return $query->where('action',EmploymentHistory::ACTION_RETIRE);
    }

    public function resignations(Builder $query){
        return $query->where('action',EmploymentHistory::ACTION_RESIGN);
    }
}
