<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Division;
use App\Models\Section;
use App\Scopes\IsDirectorate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Exception;

class DepartmentsController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $builder = Department::query();
            $scope = $request->get('scope');
            if ($scope == 'executive-director')
            {
                $builder->forExecutiveDirector();
            } else
            {
                $builder->forDirectorate();
                $directorateId = $request->get('directorateId');
                if (!empty($directorateId))
                {
                    $builder->where('directorate_id', $directorateId);
                }
            }
            $departments = $builder->get()->map(function (Department $department) {
                return $department->getDetails();
            });
            return response()->json($departments);
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function indexUnscoped(Request $request)
    {
        try
        {
            $departments = Department::query()
                                       ->get()
                                       ->map(function (Department $department) {
                                           return $department->getDetails();
                                       });
            return response()->json($departments);
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
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            $data = [
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'directorate_id' => $request->get('directorateId'),
                'created_by' => $request->get('userId'),
            ];
            Department::query()->create($data);
            return response()->json('Department created!');
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function update(Request $request)
    {
        try
        {
            $rules = [
                'id' => 'required',
                'title' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);

            $id = $request->get('id');
            $department = Department::query()->find($id);
            if (!$department)
            {
                throw new Exception('Department not found!');
            }

            $department->title = $request->get('title');
            $department->description = $request->get('description');
            $department->updated_by = $request->get('userId');

            $directorateId = $request->get('directorateId');
            if (!empty($directorateId) && $directorateId != $department->directorate_id)
            {
                $department->directorate_id = $directorateId;
            }

            $department->save();

            return response()->json('Department updated!');
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function show(Request $request)
    {
        try
        {
            $id = $request->get('departmentId');
            $builder = Department::query();
            $scope = $request->get('scope');
            if ($scope == 'executive-director')
            {
                $builder->forExecutiveDirector();
            } else
            {
                $builder->forDirectorate();
            }
            $department = $builder->find($id);
            if (!$department)
            {
                throw new Exception("Department not found!");
            }
            /*
                        $divisions = $department->divisions()
                                                ->get()
                                                ->map(function (Division $division) {
                                                    return $division->getDetails();
                                                });

                        $sections = $department->sections()
                                               ->get()
                                               ->map(function (Section $section) {
                                                   return $section->getDetails();
                                               });

            */
            $department = $department->getDetails();
            //$department->sections = $sections;
            //$department->divisions = $divisions;

            return response()->json($department);
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function delete(Request $request)
    {
        try
        {
            $id = $request->get('departmentId');
            $department = Department::query()->find($id);
            if (!$department)
            {
                throw new Exception("Department not found!");
            }
            $department->delete();
            return response()->json('Department deleted!');
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
