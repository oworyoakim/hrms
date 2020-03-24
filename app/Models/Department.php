<?php

namespace App\Models;

use App\Traits\Addressable;
use App\Traits\BelongsToDirectorate;
use App\Traits\BelongsToExecutiveSecretary;
use App\Traits\Contactable;

class Department extends Model
{
    use Addressable, Contactable, BelongsToExecutiveSecretary, BelongsToDirectorate;

    public function directorate()
    {
        return $this->belongsTo(Directorate::class, 'directorate_id');
    }

    public function sections()
    {
        return $this->hasMany(Section::class, 'department_id');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class, 'department_id');
    }
}
