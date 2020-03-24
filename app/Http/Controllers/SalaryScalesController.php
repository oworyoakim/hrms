<?php

namespace App\Http\Controllers;

use App\Models\SalaryScale;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Http\Response;

class SalaryScalesController extends Controller
{

    public function index(Request $request)
    {
        try
        {
            $data = SalaryScale::all();
            return response()->json($data);
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
                'scale' => $request->get('scale'),
                'rank' => $request->get('rank'),
                'description' => $request->get('description'),
            ];
            SalaryScale::create($data);
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
            $sscale = SalaryScale::find($id);
            if (!$sscale)
            {
                throw new Exception("Salary scale not found!");
            }
            $sscale->scale = $request->get('scale');
            $sscale->rank = $request->get('rank');
            $sscale->description = $request->get('description');
            $sscale->save();
            return response()->json('Changes Applied!');
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function delete(Request $request)
    {
        try
        {
            $id = $request->get('salary_scale_id');
            $salaryScale = SalaryScale::find($id);

            if (!$salaryScale)
            {
                throw new Exception('Salary scale not found!');
            }

            $salaryScale->delete();

            return response()->json('Changes Applied!');
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
