<?php

namespace App\Models;

use stdClass;

class Education extends Model
{
    protected $table = 'educations';

    public function employee(){
        return $this->belongsTo(Employee::class,'employee_id');
    }

    public function getDetails(){
        $eduction = new stdClass();
        $eduction->id = $this->id;
        $eduction->employeeId = $this->employee_id;
        $eduction->institution = $this->institution;
        $eduction->qualification = $this->qualification;
        $eduction->description = $this->description;
        $eduction->startMonth = $this->start_month;
        $eduction->startYear = $this->start_year;
        $eduction->endMonth = $this->end_month;
        $eduction->endYear = $this->end_year;
        $eduction->certificatePath = $this->certificatePath;
        $eduction->createdBy = $this->created_by;
        $eduction->updatedBy = $this->updated_by;

        $eduction->createdAt = $this->created_at->toDateString();
        $eduction->updatedAt = $this->updated_at->toDateString();

        return $eduction;
    }
}
