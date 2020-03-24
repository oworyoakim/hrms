<?php

namespace App\Models;

use App\Traits\Addressable;
use App\Traits\BelongsToDirectorate;
use App\Traits\BelongsToExecutiveSecretary;
use Illuminate\Database\Eloquent\Builder;
use stdClass;

class Designation extends Model
{
    use Addressable, BelongsToExecutiveSecretary, BelongsToDirectorate;
    protected $table = 'designations';

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function directorate()
    {
        return $this->belongsTo(Directorate::class, 'directorate_id');
    }

    public function salaryScale()
    {
        return $this->belongsTo(SalaryScale::class, 'salary_scale_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(Designation::class, 'supervisor_id');
    }

    public function subordinates()
    {
        return $this->hasMany(Designation::class, 'supervisor_id');
    }

    public function holders()
    {
        return $this->hasMany(Employee::class, 'designation_id');
    }

    public function delegates()
    {
        return $this->hasMany(Delegation::class, 'delegated_id');
    }

    public function delegators()
    {
        return $this->hasMany(Delegation::class, 'substantive_id');
    }

    public function scopeActive(Builder $query)
    {
        return $query->where('active', true);
    }

    public function scopeInactive(Builder $query)
    {
        return $query->where('active', false);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    public function getHolders(){
        return $this->holders()->get()->map(function ($holder){
            $employee = new stdClass();
            $employee->id = $holder->id;
            $employee->name = $holder->fullName();
            $employee->username = $holder->username;
            $employee->avatar = $holder->avatar;
            $employee->designation = new stdClass();
            $employee->designation->id = $this->id;
            $employee->designation->title = $this->title;
            return $employee;
        });
    }

}
