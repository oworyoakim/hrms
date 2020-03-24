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
            $employee_id = $request->get('employee_id');
            if (!$employee_id)
            {
                throw new Exception('Employee required!');
            }
            $experiences = Experience::query()
                                     ->where('employee_id', $employee_id)
                                     ->get()
                                     ->sortByDesc(function ($experience) {
                                         $endDate = Carbon::make("{$experience->end_year} {$experience->end_month}");
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
            $employee_id = $request->get('employee_id');
            if (!$employee_id)
            {
                throw new Exception('Employee required!');
            }
            $data = [
                'employee_id' => $employee_id,
                'company' => $request->get('company'),
                'position' => $request->get('position'),
                'description' => $request->get('description'),
                'start_month' => $request->get('start_month'),
                'start_year' => $request->get('start_year'),
                'end_month' => $request->get('end_month'),
                'end_year' => $request->get('end_year'),
            ];
            $experience = Experience::query()->create($data);
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
            $id = $request->get('id');
            $employee_id = $request->get('employee_id');
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
            $experience->start_month = $request->get('start_month');
            $experience->start_year = $request->get('start_year');
            $experience->end_month = $request->get('end_month');
            $experience->end_year = $request->get('end_year');
            $experience->save();

            return response()->json("Record Saved!");
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
