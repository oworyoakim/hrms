<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Division;
use App\Models\Section;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class DivisionsController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $builder = Division::query();
            $scope = $request->get('scope');
            if ($scope == 'executive-secretary')
            {
                $builder->forExecutiveSecretary();
            } else
            {
                $builder->forDirectorate();
                $directorateId = $request->get('directorateId');
                if ($directorateId)
                {
                    $builder->where('directorate_id', $directorateId);
                }
            }
            $departmentId = $request->get('departmentId');
            if ($departmentId)
            {
                $builder->where('department_id', $departmentId);
            }
            $divisions = $builder->get()->map(function (Division $division) {
                return $division->getDetails();
            });
            return response()->json($divisions);
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
            Division::query()->create([
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'directorate_id' => $request->get('directorateId'),
                'department_id' => $request->get('departmentId'),
                'created_by' => $request->get('userId'),
            ]);
            return response()->json('Division created!');
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
            $division = Division::query()->find($id);
            if (!$division)
            {
                throw new Exception('Division not found!');
            }

            $division->title = $request->get('title');
            $division->description = $request->get('description');
            $division->updated_by = $request->get('userId');
            $directorateId = $request->get('directorateId');

            if (!empty($directorateId) && $directorateId != $division->directorate_id)
            {
                $division->directorate_id = $directorateId;
            }
            $departmentId = $request->get('departmentId');
            if (!empty($departmentId) && $departmentId != $division->department_id)
            {
                $division->department_id = $departmentId;
            }

            $division->save();
            return response()->json('Division updated!');
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function show(Request $request)
    {
        try
        {
            $id = $request->get('divisionId');
            $builder = Division::query();
            $scope = $request->get('scope');
            if ($scope == 'executive-secretary')
            {
                $builder->forExecutiveSecretary();
            } else
            {
                $builder->forDirectorate();
            }
            $division = $builder->find($id);
            if (!$division)
            {
                throw new Exception('Division not found!');
            }
            /*
            $sections = $division->sections()->get()->map(function (Section $section) {
                return $section->getDetails();
            });
            */

            $division = $division->getDetails();
            //$division->sections = $sections;

            return response()->json($division);
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function delete(Request $request)
    {
        try
        {
            $id = $request->get('divisionId');
            $division = Division::find($id);
            if (!$division)
            {
                throw new Exception('Division not found!');
            }
            $division->sections()->delete();
            $division->employees()->delete();
            $division->delete();
            return response()->json('Changes Applied!');
        } catch (Exception $ex)
        {
            Log::error($ex->getMessage());
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
