<?php

namespace App\Models;

class Dependant extends Model
{
    protected $table = 'dependants';

    public function fullName()
    {
        return "{$this->title}  {$this->first_name}  {$this->last_name}";
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
