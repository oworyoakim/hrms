<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Directorate;
use App\Models\Division;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Exception;

class SectionsController extends Controller
{

    public function index(Request $request)
    {
        try
        {
            $builder = Section::query();
            $scope = $request->get('scope');
            if ($scope == 'executive-secretary')
            {
                $builder->forExecutiveSecretary();
            } else
            {
                $builder->forDirectorate();

                $directorateId = $request->get('directorateId');
                if (!empty($directorateId))
                {
                    $builder->where('directorate_id', $directorateId);
                }
            }

            $departmentId = $request->get('departmentId');
            if (!empty($departmentId))
            {
                $builder->where('department_id', $departmentId);
            }

            $divisionId = $request->get('divisionId');
            if (!empty($divisionId))
            {
                $builder->where('division_id', $divisionId);
            }
            $sections = $builder->get()
                                ->map(function (Section $section) {
                                    return $section->getDetails();
                                });
            return response()->json($sections);
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

            $directorateId = $request->get('directorateId');
            $departmentId = $request->get('departmentId');
            $divisionId = $request->get('divisionId');
            $department = Department::query()->find($departmentId);
            $division = Division::query()->find($divisionId);
            if ($division)
            {
                $directorateId = $division->directorate_id;
                $departmentId = $division->department_id;
            } elseif ($department)
            {
                $directorateId = $department->directorate_id;
            }

            $data = [
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'directorate_id' => $directorateId,
                'department_id' => $departmentId,
                'division_id' => $divisionId,
                'created_by' => $request->get('userId'),
            ];
            Section::query()->create($data);
            return response()->json('Section created!');
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
            $section = Section::query()->find($id);
            if (!$section)
            {
                throw new Exception('Section not found!');
            }
            $section->title = $request->get('title');
            $section->description = $request->get('description');
            $section->updated_by = $request->get('userId');
            $directorateId = $request->get('directorateId');
            $divisionId = $request->get('divisionId');
            $departmentId = $request->get('departmentId');

            if ($divisionId != $section->division_id)
            {
                $division = Division::query()->find($divisionId);
                if ($division)
                {
                    $directorateId = $division->directorate_id;
                    $departmentId = $division->department_id;
                } else
                {
                    $department = Department::query()->find($departmentId);
                    if ($department)
                    {
                        $directorateId = $department->directorate_id;
                    }
                }
            } elseif ($departmentId != $section->department_id)
            {
                $department = Department::query()->find($departmentId);
                if ($department)
                {
                    $directorateId = $department->directorate_id;
                }
            }
            $section->directorate_id = $directorateId;
            $section->department_id = $departmentId;
            $section->division_id = $divisionId;
            $section->save();
            return response()->json('Section updated!');
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function show(Request $request)
    {
        try
        {
            $id = $request->get('sectionId');
            $builder = Section::query();
            $scope = $request->get('scope');
            if ($scope == 'executive-secretary')
            {
                $builder->forExecutiveSecretary();
            } else
            {
                $builder->forDirectorate();
            }
            $section = $builder->find($id);

            if (!$section)
            {
                throw new Exception('Section not found');
            }

            $section = $section->getDetails();

            return response()->json($section);
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function delete(Request $request)
    {
        try
        {
            $id = $request->get('sectionId');
            $secrion = Section::query()->find($id);
            if (!$secrion)
            {
                throw new Exception("Section not found!");
            }
            $secrion->delete();
            return response()->json('Section deleted!');
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
