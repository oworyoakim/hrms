<?php

namespace App\Http\Controllers;

use App\Models\Experience;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Exception;

class ExperienceInfoController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $employeeId = $request->get('employeeId');
            if (!$employeeId)
            {
                throw new Exception('Employee required!');
            }
            $experiences = Experience::query()
                                     ->where('employee_id', $employeeId)
                                     ->get()
                                    ->map(function (Experience $experience){
                                        return $experience->getDetails();
                                    })
                                     ->sortByDesc(function ($experience) {
                                         $endDate = Carbon::make("{$experience->endYear} {$experience->endMonth}");
                                         return $endDate ?? Carbon::today();
                                     })
                                     ->values();
            return response()->json($experiences);
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
                'company' => 'required',
                'position' => 'required',
                'description' => 'required',
                'startMonth' => 'required',
                'startYear' => 'required',
                'employeeId' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            $employee_id = $request->get('employeeId');
            if (!$employee_id)
            {
                throw new Exception('Employee required!');
            }
            $data = [
                'employee_id' => $employee_id,
                'company' => $request->get('company'),
                'position' => $request->get('position'),
                'description' => $request->get('description'),
                'start_month' => $request->get('startMonth'),
                'start_year' => $request->get('startYear'),
                'end_month' => $request->get('endMonth'),
                'end_year' => $request->get('endYear'),
            ];
            Experience::query()->create($data);
            return response()->json("Record Saved!");
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
                'company' => 'required',
                'position' => 'required',
                'description' => 'required',
                'startMonth' => 'required',
                'startYear' => 'required',
                'employeeId' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            $id = $request->get('id');
            $employee_id = $request->get('employeeId');
            if (!$employee_id)
            {
                throw new Exception('Employee required!');
            }

            $experience = Experience::query()
                                    ->where('employee_id', $employee_id)
                                    ->find($id);

            if (!$experience)
            {
                throw new Exception('Experience not found!');
            }

            $experience->company = $request->get('company');
            $experience->position = $request->get('position');
            $experience->description = $request->get('description');
            $experience->start_month = $request->get('startMonth');
            $experience->start_year = $request->get('startYear');
            $experience->end_month = $request->get('endMonth');
            $experience->end_year = $request->get('endYear');
            $experience->save();

            return response()->json("Record Saved!");
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
