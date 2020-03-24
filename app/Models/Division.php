<?php

namespace App\Models;

use App\Traits\Addressable;
use App\Traits\BelongsToDirectorate;
use App\Traits\BelongsToExecutiveSecretary;
use App\Traits\Contactable;

class Division extends Model
{
    use Addressable, Contactable,BelongsToDirectorate,BelongsToExecutiveSecretary;

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
}
