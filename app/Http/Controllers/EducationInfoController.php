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
            $employeeId = $request->get('employeeId');
            if (!$employeeId) {
                throw new Exception('Employee required!');
            }
            $educations = Education::query()
                ->where('employee_id', $employeeId)
                ->get()
                ->map(function (Education $education){
                    return $education->getDetails();
                })
                ->sortByDesc(function ($education) {
                    $endDate = Carbon::make("{$education->endYear} {$education->endMonth}");
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
            $rules = [
                'institution' => 'required',
                'qualification' => 'required',
                'startMonth' => 'required',
                'startYear' => 'required',
                'employeeId' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            $employeeId = $request->get('employeeId');
            if (!$employeeId) {
                throw new Exception('Employee required!');
            }
            $data = [
                'employee_id' => $employeeId,
                'institution' => $request->get('institution'),
                'qualification' => $request->get('qualification'),
                'description' => $request->get('description'),
                'start_month' => $request->get('startMonth'),
                'start_year' => $request->get('startYear'),
                'end_month' => $request->get('endMonth'),
                'end_year' => $request->get('endYear'),
                'created_by' => $request->get('userId'),
            ];
            Education::query()->create($data);
            return response()->json("Education created!");
        } catch (Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function update(Request $request)
    {
        try {
            $rules = [
                'id' => 'required',
                'institution' => 'required',
                'qualification' => 'required',
                'startMonth' => 'required',
                'startYear' => 'required',
                'employeeId' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            $id = $request->get('id');
            $employeeId = $request->get('employeeId');
            if (!$employeeId) {
                throw new Exception('Employee required!');
            }

            $education = Education::query()->where('employee_id', $employeeId)->find($id);

            if (!$education) {
                throw new Exception('Education not found!');
            }

            $education->institution = $request->get('institution');
            $education->qualification = $request->get('qualification');
            $education->description = $request->get('description');
            $education->start_month = $request->get('startMonth');
            $education->start_year = $request->get('startYear');
            $education->end_month = $request->get('endMonth');
            $education->end_year = $request->get('endMear');
            $education->updated_by = $request->get('userId');

            $education->save();

            return response()->json("Record Saved!");
        } catch (Exception $ex) {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
