<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Http\Response;

class DepartmentsController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $builder = Department::with(['directorate']);
            $scope = $request->get('scope');
            if ($scope == 'executive-secretary')
            {
                $builder->forExecutiveSecretary();
            } else
            {
                $builder->forDirectorate();
                if ($directorate_id = $request->get('directorate_id'))
                {
                    $builder->where('directorate_id', $directorate_id);
                }
            }
            $departments = $builder->get();
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
            $data = [
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'directorate_id' => $request->get('directorate_id'),
            ];
            Department::create($data);
            return response()->json('Record Saved!');
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function update(Request $request)
    {
        try
        {
            $id = $request->get('id');
            $department = Department::find($id);
            if (!$department)
            {
                throw new Exception('Department not found!');
            }

            $department->title = $request->get('title');
            $department->description = $request->get('description');

            if($directorate_id = $request->get('directorate_id'))
            {
                $department->directorate_id = $directorate_id;
            }
            $department->save();

            return response()->json('Changes Applied!');
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function show(Request $request, $id)
    {
        try
        {
            $builder = Department::with(['directorate', 'sections']);
            $scope = $request->get('scope');
            if ($scope == 'executive-secretary')
            {
                $builder->forExecutiveSecretary();
            } else
            {
                $builder->forDirectorate();
            }
            $department = $builder->find($id);
            if(!$department){
                throw new Exception("Department not found!");
            }
            $data = ['department' => $department, 'scope' => $scope];
            return response()->json($data);
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(),Response::HTTP_FORBIDDEN);
        }
    }

    public function delete(Request $request)
    {
        try
        {
            $id = $request->get('department_id');
            $department = Department::find($id);
            if (!$department)
            {
                throw new Exception("Department not found!");
            }
            $department->delete();
            return response()->json('Changes Applied!');
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
