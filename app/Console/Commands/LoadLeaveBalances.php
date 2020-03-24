<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\LeaveType;
use Illuminate\Console\Command;

class LoadLeaveBalances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'load:leave-balances {employee_id?} {leave_type_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to load leave balances for each employee for the different leave types';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $employee_id = $this->argument('employee_id');
        $leave_type_id = $this->argument('leave_type_id');
        if ($employee = Employee::query()->find($employee_id))
        {
            foreach (LeaveType::all() as $leaveType)
            {
                $employee->loadLeaveBalances($leaveType->id);
            }
        } elseif ($leaveType = LeaveType::query()->find($leave_type_id))
        {
            foreach (Employee::all() as $employee)
            {
                $employee->loadLeaveBalances($leave_type_id);
            }
        } else
        {
            $leaveTypes = LeaveType::all();
            $employees = Employee::all();
            foreach ($leaveTypes as $leaveType)
            {
                foreach ($employees as $employee)
                {
                    $employee->loadLeaveBalances($leaveType->id);
                }
            }
        }
    }
}
