<?php

namespace App\Models;

/**
 * Class LeaveApplicationSetting
 * @package App\Models
 * @property int id
 * @property int designation_id
 * @property int verified_by
 * @property int approved_by
 * @property int granted_by
 */
class LeaveApplicationSetting extends Model
{
    protected $table = 'leave_application_settings';
    protected $guarded = [];
}
