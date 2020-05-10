<?php

namespace App\Models;

use stdClass;

class Bank extends Model
{
    protected $table = 'banks';

    public function employee(){
        return $this->belongsTo(Employee::class,'employee_id');
    }

    public function getDetails(){
        $bank = new stdClass();
        $bank->id = $this->id;
        $bank->employeeId = $this->employee_id;
        $bank->accountName = $this->account_name;
        $bank->accountNumber = $this->account_number;
        $bank->bankName = $this->bank_name;
        $bank->bankBranch = $this->bank_branch;
        $bank->swiftCode = $this->swift_code;
        $bank->createdBy = $this->created_by;
        $bank->updatedBy = $this->updated_by;
        $bank->createdAt = $this->created_at->toDateString();
        $bank->updatedAt = $this->updated_at->toDateString();
        return $bank;
    }
}
