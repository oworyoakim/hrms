<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Leave;
use App\Models\LeaveApplication;
use App\Models\LeaveTracker;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Exception;

class LeavesController extends Controller
{

    public function index(Request $request)
    {
        try
        {
            $builder = Leave::with(['employee']);
            if ($employee_id = $request->get('employee_id'))
            {
                $builder->where('employee_id', $employee_id);
            }
            if ($leave_type_id = $request->get('leave_type_id'))
            {
                $builder->where('leave_type_id', $leave_type_id);
            }
            $leaves = $builder->get();
            return response()->json($leaves);
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function history(Request $request)
    {
        try
        {
            $employeeId = $request->get('employeeId');
            $leaveApplicationId = $request->get('leaveApplicationId');

            if (empty($employeeId))
            {
                throw new Exception("Employee ID required!");
            }

            if (empty($leaveApplicationId))
            {
                throw new Exception("Leave Application ID required!");
            }
            // We need Employee details (full name, designation, directorate, supervisor, salary scale)
            // We need Entitlement, Number of days earned (if earned leaves),
            // Total days accumulated, Last day on leave, last work anniversary, next work anniversary
            $employee = Employee::query()->find($employeeId);
            if (empty($employee))
            {
                throw new Exception("Employee not found!");
            }
            $nextWorkAnniversary = $employee->nextWorkAnniversary();
            $lastWorkAnniversary = $employee->lastWorkAnniversary();
            $data = [
                'fullName' => $employee->fullName(),
                'designation' => ($employee->designation) ? $employee->designation->title : null,
                'directorate' => ($employee->directorate) ? $employee->directorate->title : null,
                'supervisor' => $employee->supervisor(),
                'salaryScale' => ($employee->scale) ? $employee->scale->scale : null,
                'nextWorkAnniversary' => ($nextWorkAnniversary) ? $nextWorkAnniversary->toDateString() : null,
                'lastWorkAnniversary' => ($lastWorkAnniversary) ? $lastWorkAnniversary->toDateString() : null,
            ];
            // We need Leave Type Applied for, and duration
            $leaveApplication = LeaveApplication::query()->find($leaveApplicationId);
            if (empty($leaveApplication))
            {
                throw new Exception("Leave Application not found!");
            }
            $data['leaveType'] = ($leaveApplication->leaveType) ? $leaveApplication->leaveType->title : null;
            $leaveTracker = LeaveTracker::query()
                                        ->where([
                                            'employee_id' => $employeeId,
                                            'leave_type_id' => $leaveApplication->leave_type_id,
                                            'status' => 'onleave',
                                        ])
                                        ->latest()
                                        ->first();
            $data['lastDayOnLeave'] = ($leaveTracker) ? $leaveTracker->date_on_leave : null;
            $data['entitlement'] = $employee->getEntitlement($leaveApplication->leave_type_id);
            $data['daysEarned'] = $employee->getTotalDaysEarned($leaveApplication->leave_type_id);
            $data['totalDaysAccumulated'] = $employee->getTotalRemainingDaysForEarnedLeave($leaveApplication->leave_type_id, $leaveApplication->start_date);


            return response()->json($data);
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

}
