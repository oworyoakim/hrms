<?php

namespace App\Models;

class LeaveSetting extends Model
{
    protected $table = 'leave_settings';

    public function leaveType(){
        return $this->belongsTo(LeaveSetting::class,'leave_type_id');
    }
}
