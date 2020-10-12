<?php

namespace App\Http\Controllers;

use App\HrmsHelper;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\EmploymentAction;
use App\Models\EmploymentHistory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Str;
use stdClass;

class EmployeesController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $builder = Employee::query();
            $name = $request->get('name');
            $status = $request->get('employeeStatus');
            $employment_status = $request->get('employmentStatus');
            $employment_term = $request->get('employmentTerm');
            $employment_type = $request->get('employmentType');
            $designation_id = $request->get('designationId');
            $department_id = $request->get('departmentId');
            $directorate_id = $request->get('directorateId');
            $division_id = $request->get('divisionId');
            $section_id = $request->get('sectionId');
            $scope = $request->get('scope');
            $gender = $request->get('gender');
            if ($scope == 'executive-director')
            {
                $builder->forExecutiveDirector();
            } elseif ($directorate_id)
            {
                $builder->where('directorate_id', $directorate_id);
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
                $builder->where('employee_status', $status);
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

            $employeesData = $builder->paginate(8);
            $employees = HrmsHelper::generatePagination($employeesData);
            $employees->data = $employeesData->getCollection()
                                             ->map(function (Employee $employee) {
                                                 // transform the employee object here
                                                 return $employee->getDetails();
                                             });
            return response()->json($employees);
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function indexUnscoped(Request $request)
    {
        try
        {
            $builder = Employee::query();
            $designation_id = $request->get('designationId');
            $department_id = $request->get('departmentId');
            $directorate_id = $request->get('directorateId');
            $division_id = $request->get('divisionId');
            $section_id = $request->get('sectionId');
            if ($directorate_id)
            {
                $builder->where('directorate_id', $directorate_id);
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

            $employees = $builder->get()
                                 ->map(function (Employee $employee) {
                                     // transform the employee object here
                                     return $employee->getDetails(false);
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
            $rules = [
                'title' => 'required',
                'firstName' => 'required',
                'lastName' => 'required',
                'username' => 'required|unique:employees',
                'employeeNumber' => 'required|unique:employees,employee_number',
                'designationId' => 'required',
                'userId' => 'required|unique:employees,user_id',
                'dob' => 'required|date_format:Y-m-d',
                'joinDate' => 'required|date_format:Y-m-d',
                'createdBy' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            $username = $request->get('username');
            $first_name = $request->get('firstName');
            $last_name = $request->get('lastName');

            $designation_id = $request->get('designationId');

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
                'user_id' => $request->get('userId'),
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
                'middle_name' => $request->get('middleName'),
                'other_names' => $request->get('otherNames'),
                'gender' => $request->get('gender'),
                'religion' => $request->get('religion'),
                'dob' => Carbon::parse($request->get('dob')),
                'nin' => $request->get('nin'),
                'nssf' => $request->get('nssf'),
                'tin' => $request->get('tin'),
                'employee_status' => $request->get('employeeStatus') ?: 'active',
                'employee_number' => $request->get('employeeNumber'),
                'employment_term' => $request->get('employmentTerm'),
                'employment_type' => $request->get('employmentType'),
                'date_joined' => Carbon::parse($request->get('joinDate')),
                'created_by' => $request->get('createdBy'),
                'avatar' => '/storage/images/avatar.png',
            ];

            $employee = Employee::query()->create($data);
            if (!$employee)
            {
                throw new Exception('Failed to create employee!');
            }

            $history = EmploymentHistory::query()->create([
                'employee_id' => $employee->id,
                'start_date' => $employee->date_joined,
                'action' => EmploymentHistory::ACTION_APPOINT,
                'to_designation_id' => $employee->designation_id,
            ]);
            if (!$history)
            {
                throw new Exception('Failed to create employee history record!');
            }
            Artisan::call('load:leave-balances', ['employee_id' => $employee->id]);
            DB::commit();
            return response()->json('Employee created!');
        } catch (Exception $ex)
        {
            DB::rollBack();
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function update(Request $request)
    {
        try
        {
            $id = $request->get('id');
            $employee = Employee::query()->find($id);
            if (!$employee)
            {
                throw new Exception("Employee not found!");
            }
            $rules = [
                'id' => 'required',
                'title' => 'required',
                'firstName' => 'required',
                'lastName' => 'required',
                'designationId' => 'required',
                'dob' => 'required|date_format:Y-m-d',
                'joinDate' => 'required|date_format:Y-m-d',
                'updatedBy' => 'required',
            ];
            $employeeNumber = $request->get('employeeNumber');
            $email = $request->get('email');
            $designationId = $request->get('designationId');

            if ($employeeNumber != $employee->employee_number)
            {
                $rules['employeeNumber'] = 'required|unique:employees,employee_number';
            }
            if ($email != $employee->email)
            {
                $rules['email'] = 'required|unique:employees,email';
            }
            $this->validateData($request->all(), $rules);
            $designation = Designation::query()->find($designationId);
            if (!$designation)
            {
                throw new Exception('Designation not found!');
            }
            $employee->designation_id = $designation->id;
            $employee->directorate_id = $designation->directorate_id;
            $employee->department_id = $designation->department_id;
            $employee->division_id = $designation->division_id;
            $employee->section_id = $designation->section_id;
            $employee->salary_scale_id = $designation->salary_scale_id;
            $employee->title = $request->get('title');
            $employee->first_name = $request->get('firstName');
            $employee->last_name = $request->get('lastName');
            $employee->other_names = $request->get('otherNames');
            $employee->email = $email;
            $employee->employee_number = $employeeNumber;
            $employee->nssf = $request->get('nssf');
            $employee->tin = $request->get('tin');
            $employee->nin = $request->get('nin');
            $employee->passport = $request->get('passport');
            $employee->gender = $request->get('gender');
            $employee->religion = $request->get('religion');
            $employee->marital_status = $request->get('maritalStatus');
            $employee->dob = Carbon::parse($request->get('dob'));
            $employee->date_joined = Carbon::parse($request->get('joinDate'));
            $employee->employment_type = $request->get('employmentType');
            $employee->employment_term = $request->get('employmentTerm');
            $employee->save();
            return response()->json('Employee updated!');
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }

    }

    public function show(Request $request)
    {
        try
        {
            $id = $request->get('employeeId');
            $employee = Employee::query()->find($id);
            if (!$employee)
            {
                throw new Exception("Employee with id {$id} not found!");
            }
            $data = $employee->getDetails(false);
            return response()->json($data);
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function nextId()
    {
        try
        {
            $emp = Employee::query()->latest()->first();
            if (!$emp)
            {
                $nextId = '0001';
            } else
            {
                $nextId = str_pad($emp->id + 1, 4, '0', STR_PAD_LEFT);
            }
            return response()->json($nextId);
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
