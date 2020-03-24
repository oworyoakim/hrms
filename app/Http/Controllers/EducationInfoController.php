<?php

namespace App\Http\Controllers;

use App\Models\Education;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Exception;

class EducationInfoController extends Controller
{
    public function index(Request $request)
    {
        try {
            $employee_id = $request->get('employee_id');
            if (!$employee_id) {
                throw new Exception('Employee required!');
            }
            $educations = Education::query()
                ->where('employee_id', $employee_id)
                ->get()
                ->sortByDesc(function ($education) {
                    $endDate = Carbon::make("{$education->end_year} {$education->end_month}");
                    return $endDate ?? Carbon::today();
                })
                ->values();
            return response()->json($educations);
        } catch (Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function store(Request $request)
    {
        try {
            $employee_id = $request->get('employee_id');
            if (!$employee_id) {
                throw new Exception('Employee required!');
            }
            $data = [
                'employee_id' => $employee_id,
                'institution' => $request->get('institution'),
                'qualification' => $request->get('qualification'),
                'description' => $request->get('description'),
                'start_month' => $request->get('start_month'),
                'start_year' => $request->get('start_year'),
                'end_month' => $request->get('end_month'),
                'end_year' => $request->get('end_year'),
            ];
            $education = Education::query()->create($data);
            return response()->json("Record Saved!");
        } catch (Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function update(Request $request)
    {
        try {
            $id = $request->get('id');
            $employee_id = $request->get('employee_id');
            if (!$employee_id) {
                throw new Exception('Employee required!');
            }

            $education = Education::query()->where('employee_id', $employee_id)->find($id);

            if (!$education) {
                throw new Exception('Education not found!');
            }

            $education->institution = $request->get('institution');
            $education->qualification = $request->get('qualification');
            $education->description = $request->get('description');
            $education->start_month = $request->get('start_month');
            $education->start_year = $request->get('start_year');
            $education->end_month = $request->get('end_month');
            $education->end_year = $request->get('end_year');
            $education->save();

            return response()->json("Record Saved!");
        } catch (Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
