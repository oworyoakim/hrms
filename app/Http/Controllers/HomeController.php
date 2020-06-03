<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Designation;
use App\Models\Directorate;
use App\Models\Division;
use App\Models\DocumentCategory;
use App\Models\DocumentType;
use App\Models\Employee;
use App\Models\EmployeeStatus;
use App\Models\EmploymentStatus;
use App\Models\EmploymentTerm;
use App\Models\EmploymentType;
use App\Models\Gender;
use App\Models\Leave;
use App\Models\LeaveApplication;
use App\Models\LeaveApplicationSetting;
use App\Models\LeaveType;
use App\Models\MaritalStatus;
use App\Models\Relationship;
use App\Models\Religion;
use App\Models\SalaryScale;
use App\Models\Section;
use App\Models\Title;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use stdClass;

class HomeController extends Controller
{
    public function canLogin(Request $request)
    {
        $data = ['canLogin' => true];
        try
        {
            $userId = $request->get('userId');
            $employee = Employee::query()->where('user_id', $userId)->first();
            if (!empty($employee))
            {
                $data['canLogin'] = $employee->canLogin();
            }
            return response()->json($data);
        } catch (Exception $ex)
        {
            $data['error'] = $ex->getMessage();
            return response()->json($data,Response::HTTP_FORBIDDEN);
        }
    }

    public function getDashboardStatistics()
    {
        try
        {
            $totalEmployees = 0;
            $statistics = Collection::make();

            // Executive Secretary's Office
            $stat = new stdClass();
            $stat->directorate = 'Executive Secretary';
            // male
            $builder = Employee::forExecutiveSecretary();
            $builder->where('gender', 'male');
            $stat->male = $builder->count();
            // female
            $builder = Employee::forExecutiveSecretary();
            $builder->where('gender', 'female');
            $stat->female = $builder->count();
            // other
            $builder = Employee::forExecutiveSecretary();
            $builder->where('gender', 'other');
            $stat->other = $builder->count();

            $totalEmployees += ($stat->male + $stat->female + $stat->other);
            $statistics->push($stat);

            // Directorates
            foreach (Directorate::all() as $directorate)
            {
                $stat = new stdClass();
                $stat->directorate = $directorate->title;
                // male
                $builder = Employee::query();
                $builder->where('directorate_id', $directorate->id);
                $builder->where('gender', 'male');
                $stat->male = $builder->count();
                // female
                $builder = Employee::query();
                $builder->where('directorate_id', $directorate->id);
                $builder->where('gender', 'female');
                $stat->female = $builder->count();
                // other
                $builder = Employee::query();
                $builder->where('directorate_id', $directorate->id);
                $builder->where('gender', 'other');
                $stat->other = $builder->count();

                $totalEmployees += ($stat->male + $stat->female + $stat->other);
                $statistics->push($stat);
            }

            $data = [
                'employeeStatistics' => $statistics,
                'totalEmployees' => $totalEmployees,
                'totalLeavesUpcoming' => Leave::pending()->count(),
                'totalLeavesOngoing' => Leave::ongoing()->count(),
                'totalLeaveApplications' => LeaveApplication::query()->whereIn('status', ['pending', 'approved'])->count(),
            ];
            return response()->json($data);
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function getFormSelectionsOptions(Request $request)
    {
        try
        {
            $departmentsBuilder = Department::query();
            $divisionsBuilder = Division::query();
            $sectionsBuilder = Section::query();
            $designationsBuilder = Designation::query();

            $scope = $request->get('scope');
            /*
            if ($scope == 'executive-secretary')
            {
                $departmentsBuilder->forExecutiveSecretary();
                $divisionsBuilder->forExecutiveSecretary();
                $sectionsBuilder->forExecutiveSecretary();
            } else
            {
                $departmentsBuilder->forDirectorate();
                $divisionsBuilder->forDirectorate();
                $sectionsBuilder->forDirectorate();
            }
            */
            if ($directorate_id = $request->get('directorateId'))
            {
                $departmentsBuilder->where('directorate_id', $directorate_id);
                $divisionsBuilder->where('directorate_id', $directorate_id);
                $sectionsBuilder->where('directorate_id', $directorate_id);
                $designationsBuilder->where('directorate_id', $directorate_id);
            }
            if ($department_id = $request->get('departmentId'))
            {
                $divisionsBuilder->where('department_id', $department_id);
                $sectionsBuilder->where('department_id', $department_id);
                $designationsBuilder->where('department_id', $department_id);
            }
            if ($division_id = $request->get('divisionId'))
            {
                $sectionsBuilder->where('division_id', $division_id);
                $designationsBuilder->where('division_id', $division_id);
            }
            if ($section_id = $request->get('sectionId'))
            {
                $designationsBuilder->where('section_id', $section_id);
            }

            $designations = $designationsBuilder->get()
                                                ->map(function ($item) {
                                                    $designation = new stdClass();
                                                    $designation->id = $item->id;
                                                    $designation->title = $item->title;
                                                    $designation->directorateId = $item->directorate_id;
                                                    $designation->departmentId = $item->department_id;
                                                    $designation->divisionId = $item->division_id;
                                                    $designation->sectionId = $item->section_id;
                                                    $designation->numHolders = $item->holders()->count();
                                                    $designation->maxHolders = intval($item->max_holders);
                                                    return $designation;
                                                });
            $emp = Employee::query()->latest()->first();
            if (!$emp)
            {
                $nextId = '0001';
            } else
            {
                $nextId = str_pad($emp->id + 1, 4, '0', STR_PAD_LEFT);
            }
            $data = [
                'directorates' => ($scope == 'executive-secretary') ? [] : Directorate::all(['id', 'title']),
                'departments' => $departmentsBuilder->get(['id', 'title', 'directorate_id as directorateId']),
                'divisions' => $divisionsBuilder->get(['id', 'title', 'directorate_id as directorateId', 'department_id as departmentId']),
                'sections' => $sectionsBuilder->get(['id', 'title', 'directorate_id as directorateId', 'department_id as departmentId', 'division_id as divisionId']),
                'designations' => $designations,
                'genders' => Gender::all(['id', 'slug', 'title']),
                'religions' => Religion::all(['id', 'title']),
                'relationships' => Relationship::all(['id', 'slug', 'title']),
                'titles' => Title::all(['id', 'title', 'slug']),
                'maritalStatuses' => MaritalStatus::all(['id', 'title', 'description']),
                'employmentTypes' => EmploymentType::all(['id', 'slug', 'title']),
                'employmentTerms' => EmploymentTerm::all(['id', 'slug', 'title']),
                'employmentStatuses' => EmploymentStatus::all(['id', 'slug', 'title']),
                'employeeStatuses' => EmployeeStatus::all(['id', 'slug', 'title']),
                'documentCategories' => DocumentCategory::all(['id', 'title', 'non_employee'])->map(function ($item) {
                    $category = new stdClass();
                    $category->id = $item->id;
                    $category->title = $item->title;
                    $category->nonEmployee = !!$item->non_employee;
                    return $category;
                }),
                'documentTypes' => DocumentType::all(['id', 'title', 'category_id as categoryId']),
                'leaveTypes' => LeaveType::active()->get(['id', 'title']),
                'salaryScales' => SalaryScale::all(['id', 'scale', 'rank']),
                'nextEmployeeId' => $nextId,
            ];
            return response()->json($data);
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
