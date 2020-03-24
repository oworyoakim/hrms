<?php


namespace App\Traits;


use Illuminate\Database\Eloquent\Builder;

trait BelongsToExecutiveSecretary
{
    public function scopeForExecutiveSecretary(Builder $builder){
        return $builder->whereNull('directorate_id');
    }
}
