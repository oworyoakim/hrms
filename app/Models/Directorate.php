<?php

namespace App\Models;

use App\Scopes\IsDirectorate;
use App\Traits\Addressable;
use App\Traits\Contactable;

class Directorate extends Model
{
    use Addressable, Contactable;

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

    public function departments()
    {
        return $this->hasMany(Department::class, 'directorate_id');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class, 'directorate_id');
    }


}
