<?php

namespace App\Models;

use stdClass;

class Experience extends Model
{
    protected $table = 'experiences';

    public function employee(){
        return $this->belongsTo(Employee::class,'employee_id');
    }

    public function getDetails(){
        $experience = new stdClass();
        $experience->id = $this->id;
        $experience->employeeId = $this->employee_id;
        $experience->company = $this->company;
        $experience->position = $this->position;
        $experience->description = $this->description;
        $experience->startMonth = $this->start_month;
        $experience->startYear = $this->start_year;
        $experience->endMonth = $this->end_month;
        $experience->endYear = $this->end_year;
        $experience->recommendationPath = $this->recommendationPath;
        $experience->createdBy = $this->created_by;
        $experience->updatedBy = $this->updated_by;

        $experience->createdAt = $this->created_at->toDateString();
        $experience->updatedAt = $this->updated_at->toDateString();

        return $experience;
    }
}
