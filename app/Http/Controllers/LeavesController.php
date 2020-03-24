<?php

namespace App\Http\Controllers;

use App\Models\Leave;
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

}
