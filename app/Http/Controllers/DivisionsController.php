<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Division;
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
            $builder = Division::with(['directorate', 'department']);
            $scope = $request->get('scope');
            if ($scope == 'executive-secretary')
            {
                $builder->forExecutiveSecretary();
            } else
            {
                $builder->forDirectorate();
                $directorate_id = $request->get('directorate_id');
                if ($directorate_id)
                {
                    $builder->where('directorate_id', $directorate_id);
                }
            }
            $department_id = $request->get('department_id');
            if ($department_id)
            {
                $builder->where('department_id', $department_id);
            }
            $divisions = $builder->get();
            return response()->json($divisions);
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function show(Request $request, $id)
    {
        try
        {
            $builder = Division::with(['directorate', 'department', 'sections']);
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
            $data = [
                'division' => $division,
                'scope' => $scope,
            ];
            return response()->json($data);
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function delete(Request $request)
    {
        try
        {
            $id = $request->get('division_id');
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
