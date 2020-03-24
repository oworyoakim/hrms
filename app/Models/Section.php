<?php

namespace App\Models;

use App\Traits\BelongsToDirectorate;
use App\Traits\BelongsToExecutiveSecretary;

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

}
