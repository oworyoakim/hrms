<?php


namespace App\Traits;


use Illuminate\Database\Eloquent\Builder;

trait BelongsToDirectorate
{
    public function scopeForDirectorate(Builder $builder){
        return $builder->whereNotNull('directorate_id');
    }
}
