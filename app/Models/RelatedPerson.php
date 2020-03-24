<?php

namespace App\Models;

use App\Traits\Addressable;
use App\Traits\Contactable;

class RelatedPerson extends Model
{
    use Addressable, Contactable;

    protected $table = 'related_persons';
    protected $with = ['relationship'];

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
}
