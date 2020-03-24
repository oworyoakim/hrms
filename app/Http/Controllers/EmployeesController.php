<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use App\Models\Employee;
use App\Models\EmploymentAction;
use App\Models\EmploymentHistory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Exception;

class EmployeesController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $builder = Employee::with(['designation', 'department', 'directorate']);
            $name = $request->get('name');
            $status = $request->get('status');
            $employment_status = $request->get('employment_status');
            $employment_term = $request->get('employment_term');
            $employment_type = $request->get('employment_type');
            $designation_id = $request->get('designation_id');
            $department_id = $request->get('department_id');
            $directorate_id = $request->get('directorate_id');
            $division_id = $request->get('division_id');
            $section_id = $request->get('section_id');
            $scope = $request->get('scope');
            $gender = $request->get('gender');
            if ($scope == 'executive-secretary')
            {
                $builder->forExecutiveSecretary();
            } else
            {
                $builder->forDirectorate();
                if ($directorate_id)
                {
                    $builder->where('directorate_id', $directorate_id);
                }
            }

            if ($name)
            {
                $builder->where(function (Builder $query) use ($name) {
                    $names = explode(' ', $name);
                    foreach ($names as $n)
                    {
                        $query->whereRaw("first_name LIKE '%{$n}%'")
                              ->orWhereRaw("last_name LIKE '%{$n}%'")
                              ->orWhereRaw("middle_name LIKE '%{$n}%'");
                    }
                });
            }
            if ($status)
            {
                $builder->where('status', $status);
            }
            if ($employment_status)
            {
                $builder->where('employment_status', $employment_status);
            }
            if ($employment_term)
            {
                $builder->where('employment_term', $employment_term);
            }
            if ($employment_type)
            {
                $builder->where('employment_type', $employment_type);
            }
            if ($designation_id)
            {
                $builder->where('designation_id', $designation_id);
            }
            if ($department_id)
            {
                $builder->where('department_id', $department_id);
            }

            if ($division_id)
            {
                $builder->where('division_id', $division_id);
            }
            if ($section_id)
            {
                $builder->where('section_id', $section_id);
            }
            if ($gender)
            {
                $builder->where('gender', $gender);
            }

            $employees = $builder->get()
                                 ->map(function (Employee $emp) {
                                     // transform the employee object here
                                     $emp->fullName = $emp->fullName();
                                     return $emp;
                                 });
            return response()->json($employees);
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function store(Request $request)
    {
        try
        {
            $username = $request->get('username');
            $first_name = $request->get('first_name');
            $last_name = $request->get('last_name');

            $designation_id = $request->get('designation_id');

            if (!$designation_id)
            {
                throw new Exception('Designation required!');
            }

            $designation = Designation::query()->find($designation_id);

            if (!$designation)
            {
                throw new Exception('Designation not found!');
            }

            if ($designation->holders()->count() >= $designation->max_holders)
            {
                throw new Exception('This position is filled up. Contact Head of HR!');
            }

            DB::beginTransaction();

            $data = [
                'user_id' => $request->get('user_id'),
                'directorate_id' => $designation->directorate_id,
                'department_id' => $designation->department_id,
                'division_id' => $designation->division_id,
                'section_id' => $designation->section_id,
                'designation_id' => $designation->id,
                'salary_scale_id' => $designation->salary_scale_id,
                'username' => $username,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'title' => $request->get('title'),
                'middle_name' => $request->get('middle_name'),
                'gender' => $request->get('gender'),
                'religion_id' => $request->get('religion_id'),
                'dob' => Carbon::parse($request->get('dob')),
                'nin' => $request->get('nin'),
                'nssf' => $request->get('nssf'),
                'tin' => $request->get('tin'),
                'employee_status' => $request->get('employee_status') ?: 'active',
                'employee_number' => $request->get('employee_number'),
                'employment_term' => $request->get('employment_term'),
                'employment_type' => $request->get('employment_type'),
                'date_joined' => Carbon::parse($request->get('date_joined')),
                'created_by' => $request->get('created_by'),
                'avatar' => '/images/avatar.png',
            ];

            $employee = Employee::query()->create($data);
            if (!$employee)
            {
                throw new Exception('Failed to create employee!');
            }
            $action = EmploymentAction::where('title', 'Appointment')->first();
            if (!$action)
            {
                throw new Exception('Employee creation action rejected. Contact admin for help!');
            }
            $history = EmploymentHistory::create([
                'employee_id' => $employee->id,
                'start_date' => $employee->date_joined,
                'action_id' => $action->id,
                'to_designation_id' => $employee->designation_id,
            ]);
            if (!$history)
            {
                throw new Exception('Failed to create employee history record!');
            }
            Artisan::call('load:leave-balances', ['employee_id' => $employee->id]);
            DB::commit();
            return response()->json('Record Saved!');
        } catch (Exception $ex)
        {
            DB::rollBack();
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function update(Request $request)
    {
        return response()->json('Ok');
    }
}
