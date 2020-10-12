<?php

namespace App\Models;

use App\Traits\Addressable;
use App\Traits\BelongsToDirectorate;
use App\Traits\BelongsToExecutiveDirector;
use App\Traits\Contactable;
use App\Traits\HasAHead;
use stdClass;

class Division extends Model
{
    use Addressable, Contactable,BelongsToDirectorate,BelongsToExecutiveDirector, HasAHead;

    public function directorate()
    {
        return $this->belongsTo(Directorate::class, 'directorate_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function sections(){
        return $this->hasMany(Section::class,'division_id');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class, 'division_id');
    }

    public function getDetails()
    {
        $division = new stdClass();
        $division->id = $this->id;
        $division->title = $this->title;
        $division->description = $this->description;
        $division->directorateId = $this->directorate_id ?: null;
        $division->directorate = $this->directorate ? $this->directorate->getDetails() : null;
        $division->departmentId = $this->department_id ?: null;
        $division->department = $this->department ? $this->department->getDetails() : null;
        $division->createdBy = $this->created_by;
        $division->updatedBy = $this->updated_by;
        $division->createdAt = $this->created_at->toDateString();
        $division->updatedAt = $this->updated_at->toDateString();
        $division->head = $this->getHeadOf('division');
        return $division;
    }

}
