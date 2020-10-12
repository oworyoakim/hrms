<?php


namespace App\Traits;


use App\Models\Designation;
use stdClass;

trait HasAHead
{
    /**
     * @return stdClass|null
     */
    public function getHeadOf($level)
    {
        $designation = Designation::query()
                                  ->where('heads', $level)
                                  ->where('heads_id', $this->id)
                                  ->first();
        if (!$designation)
        {
            return null;
        }
        $holder = $designation->holders()->first();
        if (!$holder)
        {
            return null;
        }
        $employee = $holder->getDetails(false);
        $employee->designation = new stdClass();
        $employee->designation->id = $designation->id;
        $employee->designation->title = $designation->title;
        return $employee;
    }
}
