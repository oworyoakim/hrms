<?php

namespace App\Http\Controllers;

use App\Models\SalaryScale;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Exception;

class SalaryScalesController extends Controller
{

    public function index(Request $request)
    {
        try
        {
            $salaryScales = SalaryScale::all()->map(function (SalaryScale $scale){
                return $scale->getDetails();
            });
            return response()->json($salaryScales);
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
                'scale' => 'required',
                'rank' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            $data = [
                'scale' => $request->get('scale'),
                'rank' => $request->get('rank'),
                'description' => $request->get('description'),
                'created_by' => $request->get('userId'),
            ];
            SalaryScale::query()->create($data);
            return response()->json('Salary scale created!');
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
                'scale' => 'required',
                'rank' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);

            $id = $request->get('id');
            $salaryScale = SalaryScale::query()->find($id);

            if (!$salaryScale)
            {
                throw new Exception("Salary scale not found!");
            }

            $salaryScale->scale = $request->get('scale');
            $salaryScale->rank = $request->get('rank');
            $salaryScale->description = $request->get('description');
            $salaryScale->updated_by = $request->get('userId');

            $salaryScale->save();

            return response()->json('Salary scale updated!');
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function delete(Request $request)
    {
        try
        {
            $id = $request->get('salaryScaleId');
            $salaryScale = SalaryScale::query()->find($id);

            if (!$salaryScale)
            {
                throw new Exception('Salary scale not found!');
            }

            $salaryScale->delete();

            return response()->json('Salary scale deleted!');
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
