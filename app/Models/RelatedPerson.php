<?php

namespace App\Models;

use App\Traits\Addressable;
use App\Traits\Contactable;
use stdClass;

class RelatedPerson extends Model
{
    use Addressable;

    protected $table = 'related_persons';
    protected $with = ['relationship'];
    protected $dates = ['dob'];

    public function fullName()
    {
        return $this->title . ' ' . $this->first_name . ' ' . $this->last_name;
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function relationship()
    {
        return $this->belongsTo(Relationship::class, 'relationship_id');
    }

    public function getDetails(){
        $person = new stdClass();
        $person->id = $this->id;
        $person->employeeId = $this->employee_id;
        $person->firstName = $this->first_name;
        $person->lastName = $this->last_name;
        $person->middleName = $this->middle_name;
        $person->fullName = $this->fullName();
        $person->phone = $this->phone;
        $person->email = $this->email;
        $person->gender = $this->gender;
        $person->title = $this->title;
        $person->emergency = !!$this->emergency;
        $person->dependant = !!$this->dependant;
        $person->isNextOfKin = !!$this->is_next_of_kin;
        $person->nin = $this->nin;
        $person->dob = ($this->dob) ? $this->dob->toDateString() : null;
        $person->relationshipId = $this->relationship_id;
        $person->relationship = ($this->relationship) ? $this->relationship->getDetails() : null;
        $person->createdBy = $this->created_by;
        $person->updatedBy = $this->updated_by;
        $person->createdAt = $this->created_at->toDateString();
        $person->updatedAt = $this->updated_at->toDateString();
        return $person;
    }
}
