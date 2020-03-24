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
use App\Models\MaritalStatus;
use App\Models\Relationship;
use App\Models\Religion;
use App\Models\Role;
use App\Models\Section;
use App\Models\Title;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use stdClass;

class HomeController extends Controller
{
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
                'totalLeaveApplications' => LeaveApplication::whereIn('status', ['pending', 'approved'])->count(),
            ];
            return response()->json($data);
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(),Response::HTTP_FORBIDDEN);
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
            if ($scope == 'executive-secretary')
            {
                $departmentsBuilder->forExecutiveSecretary();
                $divisionsBuilder->forExecutiveSecretary();
                $sectionsBuilder->forExecutiveSecretary();
                $designationsBuilder->forExecutiveSecretary();
            } else
            {
                $departmentsBuilder->forDirectorate();
                $divisionsBuilder->forDirectorate();
                $sectionsBuilder->forDirectorate();
                $designationsBuilder->forDirectorate();
                if ($directorate_id = $request->get('directorate_id'))
                {
                    $departmentsBuilder->where('directorate_id', $directorate_id);
                    $divisionsBuilder->where('directorate_id', $directorate_id);
                    $sectionsBuilder->where('directorate_id', $directorate_id);
                    $designationsBuilder->where('directorate_id', $directorate_id);
                }
            }

            if ($department_id = $request->get('department_id'))
            {
                $divisionsBuilder->where('department_id', $department_id);
                $sectionsBuilder->where('department_id', $department_id);
                $designationsBuilder->where('department_id', $department_id);
            }
            if ($division_id = $request->get('division_id'))
            {
                $sectionsBuilder->where('division_id', $division_id);
                $designationsBuilder->where('division_id', $division_id);
            }
            if ($section_id = $request->get('section_id'))
            {
                $designationsBuilder->where('section_id', $section_id);
            }
            $designations = $designationsBuilder->get(['id', 'title', 'max_holders'])->map(function ($item) {
                $designation = new stdClass();
                $designation->id = $item->id;
                $designation->title = $item->title;
                $designation->numHolders = $item->holders()->count();
                $designation->maxHolders = intval($item->max_holders);
                return $designation;
            });
            $data = [
                'directorates' => ($scope == 'executive-secretary') ? [] : Directorate::all(['id', 'title']),
                'departments' => $departmentsBuilder->get(['id', 'title']),
                'divisions' => $divisionsBuilder->get(['id', 'title']),
                'sections' => $sectionsBuilder->get(['id', 'title']),
                'designations' => $designations,
                'genders' => Gender::all(['slug', 'title']),
                'religions' => Religion::all(['id', 'title']),
                'relationships' => Relationship::all(['id','slug','title']),
                'titles' => Title::all(['id', 'title', 'slug']),
                'maritalStatuses' => MaritalStatus::all(['id', 'title', 'description']),
                'employmentTypes' => EmploymentType::all(['slug', 'title']),
                'employmentTerms' => EmploymentTerm::all(['slug', 'title']),
                'employmentStatuses' => EmploymentStatus::all(['slug', 'title']),
                'employeeStatuses' => EmployeeStatus::all(['slug', 'title']),
                'documentCategories' => DocumentCategory::all(['id', 'title','non_employee'])->map(function($item){
                    $category = new stdClass();
                    $category->id = $item->id;
                    $category->title = $item->title;
                    $category->nonEmployee = !!$item->non_employee;
                    return $category;
                }),
                'documentTypes' => DocumentType::all(['id', 'title','category_id as categoryId']),
            ];
            return response()->json($data);
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
