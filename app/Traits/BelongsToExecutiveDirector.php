<?php


namespace App\Traits;


use Illuminate\Database\Eloquent\Builder;

trait BelongsToExecutiveDirector
{
    public function scopeForExecutiveDirector(Builder $builder){
        return $builder->whereNull('directorate_id');
    }
}
