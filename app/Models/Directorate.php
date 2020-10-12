<?php

namespace App\Models;

use App\Scopes\IsDirectorate;
use App\Traits\Addressable;
use App\Traits\Contactable;
use App\Traits\HasAHead;
use stdClass;

class Directorate extends Model
{
    use Addressable, Contactable, HasAHead;

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new IsDirectorate);
    }

    public function divisions()
    {
        return $this->hasMany(Division::class, 'directorate_id');
    }

    public function departments()
    {
        return $this->hasMany(Department::class, 'directorate_id');
    }

    public function sections()
    {
        return $this->hasMany(Section::class, 'directorate_id');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class, 'directorate_id');
    }

    public function getDetails()
    {
        $directorate = new stdClass();
        $directorate->id = $this->id;
        $directorate->title = $this->title;
        $directorate->description = $this->description;
        $directorate->createdBy = $this->created_by;
        $directorate->updatedBy = $this->updated_by;
        $directorate->createdAt = $this->created_at->toDateString();
        $directorate->updatedAt = $this->updated_at->toDateString();
        $directorate->head = $this->getHeadOf('directorate');
        return $directorate;
    }


}
