<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class Resignation extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_DECLINED = 'declined';
    const STATUS_GRANTED = 'granted';

    protected $dates = ['start_date','approved_at','approved_start_date'];

    public function employee(){
        return $this->belongsTo(Employee::class,'employee_id');
    }

    public function approved(Builder $builder){
        return $builder->where('status',Resignation::STATUS_APPROVED);
    }

    public function declined(Builder $builder){
        return $builder->where('status',Resignation::STATUS_DECLINED);
    }

    public function grantable(Builder $builder){
        return $builder->whereIn('status',[Resignation::STATUS_PENDING, Resignation::STATUS_APPROVED]);
    }

    public function granted(Builder $builder){
        return $builder->where('status',Resignation::STATUS_GRANTED);
    }

}
