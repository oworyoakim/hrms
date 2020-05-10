<?php

namespace App\Models;

use App\Traits\BelongsToDirectorate;
use App\Traits\BelongsToExecutiveSecretary;
use stdClass;

class Section extends Model
{
    use BelongsToExecutiveSecretary,BelongsToDirectorate;

    public function directorate()
    {
        return $this->belongsTo(Directorate::class, 'directorate_id');
    }
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

    public function getDetails()
    {
        $section = new stdClass();
        $section->id = $this->id;
        $section->title = $this->title;
        $section->description = $this->description;
        $section->directorateId = $this->directorate_id ?: null;
        $section->directorate = $this->directorate ? $this->directorate->getDetails() : null;
        $section->departmentId = $this->department_id ?: null;
        $section->department = $this->department ? $this->department->getDetails() : null;
        $section->divisionId = $this->division_id ?: null;
        $section->division = $this->division ? $this->division->getDetails() : null;
        $section->createdBy = $this->created_by;
        $section->updatedBy = $this->updated_by;
        $section->createdAt = $this->created_at->toDateString();
        $section->updatedAt = $this->updated_at->toDateString();
        return $section;
    }

}
