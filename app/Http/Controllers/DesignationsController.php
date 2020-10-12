<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Designation;
use App\Models\Directorate;
use App\Models\Division;
use App\Models\LeaveApplicationSetting;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Exception;

class DesignationsController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $builder = Designation::query();
            $scope = $request->get('scope');
            if ($scope == 'executive-director')
            {
                $builder->forExecutiveDirector();
            } else
            {
                //$builder->forDirectorate();
                if ($directorate_id = $request->get('directorateId'))
                {
                    $builder->where('directorate_id', $directorate_id);
                }
            }
            if ($department_id = $request->get('departmentId'))
            {
                $builder->where('department_id', $department_id);
            }

            if ($division_id = $request->get('divisionId'))
            {
                $builder->where('division_id', $division_id);
            }

            if ($section_id = $request->get('sectionId'))
            {
                $builder->where('section_id', $section_id);
            }

            if ($salary_scale_id = $request->get('salaryScaleId'))
            {
                $builder->where('salary_scale_id', $salary_scale_id);
            }

            $designations = $builder->get()->map(function (Designation $designation) {
                return $designation->getDetails();
            });

            return response()->json($designations);
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
                'shortName' => 'required|unique:designations,short_name',
                'salaryScaleId' => 'required',
                'maxHolders' => 'required',
                'userId' => 'required',
            ];
            $heads = $request->get('heads');
            $section_id = $request->get('sectionId');
            $division_id = $request->get('divisionId');
            $department_id = $request->get('departmentId');
            $directorate_id = $request->get('directorateId');
            $headsId = null;
            if (!empty($heads))
            {
                if ($heads == 'executive-director-office')
                {
                    $headsId = 1;
                } elseif ($heads == 'directorate')
                {
                    $rules['directorateId'] = 'required|numeric';
                    $headsId = $directorate_id;
                }
                if ($heads == 'department')
                {
                    $rules['departmentId'] = 'required|numeric';
                    $headsId = $department_id;
                }
                if ($heads == 'division')
                {
                    $rules['divisionId'] = 'required|numeric';
                    $headsId = $division_id;
                }
                if ($heads == 'section')
                {
                    $rules['sectionId'] = 'required|numeric';
                    $headsId = $section_id;
                }
                $designationIsHead = Designation::query()->where('heads', $heads)->where('heads_id', $headsId)->first();
                if ($designationIsHead)
                {
                    throw new Exception("This {$heads} already has a head designation!");
                }
            }
            $this->validateData($request->all(), $rules);
            $data = [
                'title' => $request->get('title'),
                'short_name' => $request->get('shortName'),
                'description' => $request->get('description'),
                'summary' => $request->get('summary'),
                'salary_scale_id' => $request->get('salaryScaleId'),
                'section_id' => $request->get('sectionId'),
                'max_holders' => $request->get('maxHolders'),
                'probational' => $request->get('probational'),
                'probation_period' => $request->get('probationPeriod'),
                'supervisor_id' => $request->get('supervisorId'),
            ];

            $designation = new Designation($data);
            $designation->heads = $heads;
            $designation->heads_id = $headsId;

            // section level
            if ($section_id && $section = Section::query()->find($section_id))
            {
                $designation->section_id = $section_id;

                if ($section->division_id)
                {
                    $designation->division_id = $section->division_id;
                }
                if ($section->department_id)
                {
                    $designation->department_id = $section->department_id;
                }
                if ($section->directorate_id)
                {
                    $designation->directorate_id = $section->directorate_id;
                }
            }

            // division level
            if ($division_id && $division = Division::query()->find($division_id))
            {
                if (!$designation->division_id)
                {
                    $designation->division_id = $division_id;
                } elseif ($designation->division_id != $division->id)
                {
                    throw new Exception('The selected section does not belong to the selected division');
                }


                if (!$designation->department_id && $division->department_id)
                {
                    $designation->department_id = $division->department_id;
                } elseif ($designation->department_id != $division->department_id)
                {
                    throw new Exception('The selected division does not belong to the same department with the selected section');
                }
                if (!$designation->directorate_id && $division->directorate_id)
                {
                    $designation->directorate_id = $division->directorate_id;
                } elseif ($designation->directorate_id != $division->directorate_id)
                {
                    throw new Exception('The selected division does not belong to the same directorate with the selected section');
                }
            }
            // department level
            if ($department_id && $department = Department::query()->find($department_id))
            {
                if (!$designation->department_id)
                {
                    $designation->department_id = $department_id;
                } elseif ($designation->department_id != $department->id)
                {
                    throw new Exception('The selected division and/or section does not belong to the selected department');
                }
                if (!$designation->directorate_id && $department->directorate_id)
                {
                    $designation->directorate_id = $department->directorate_id;
                } elseif ($designation->directorate_id != $department->directorate_id)
                {
                    throw new Exception('The selected division and/or section does not belong to the same directorate with the selected department');
                }
            }

            // directorate level
            if ($directorate_id && $directorate = Directorate::query()->find($directorate_id))
            {
                if (!$designation->directorate_id)
                {
                    $designation->directorate_id = $directorate_id;
                } elseif ($designation->directorate_id != $directorate->id)
                {
                    throw new Exception('The selected department, division, or section does not belong to the selected directorate');
                }
            }

            DB::beginTransaction();

            $designation->save();

            $designation->refresh();

            LeaveApplicationSetting::query()->create([
                'designation_id' => $designation->id,
                'verified_by' => $request->get('defaultLeaveApplicationVerifier'),
                'approved_by' => $designation->supervisor_id,
                'granted_by' => $request->get('defaultLeaveApplicationGranter'),
            ]);

            DB::commit();

            return response()->json('Designation Created!');

        } catch (Exception $ex)
        {
            DB::rollBack();
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function update(Request $request)
    {
        try
        {
            $id = $request->get('id');
            $designation = Designation::query()->find($id);
            if (!$designation)
            {
                throw new Exception('Designation not found!');
            }

            $rules = [
                'id' => 'required',
                'title' => 'required',
                'shortName' => 'required',
                'salaryScaleId' => 'required',
                'maxHolders' => 'required',
                'userId' => 'required',
            ];

            $heads = $request->get('heads');
            $section_id = $request->get('sectionId');
            $division_id = $request->get('divisionId');
            $department_id = $request->get('departmentId');
            $directorate_id = $request->get('directorateId');
            $headsId = null;
            if (!empty($heads))
            {
                if ($heads == 'executive-director-office')
                {
                    $headsId = 1;
                } elseif ($heads == 'directorate')
                {
                    $rules['directorateId'] = 'required|numeric';
                    $headsId = $directorate_id;
                }
                if ($heads == 'department')
                {
                    $rules['departmentId'] = 'required|numeric';
                    $headsId = $department_id;
                }
                if ($heads == 'division')
                {
                    $rules['divisionId'] = 'required|numeric';
                    $headsId = $division_id;
                }
                if ($heads == 'section')
                {
                    $rules['sectionId'] = 'required|numeric';
                    $headsId = $section_id;
                }
                $designationIsHead = Designation::query()->where('heads', $heads)->where('heads_id', $headsId)->first();
                if ($designationIsHead && $designationIsHead->id != $designation->id)
                {
                    throw new Exception("This {$heads} already has a head designation!");
                }
            }

            $this->validateData($request->all(), $rules);

            $shortName = $request->get('shortName');
            if ($designation->short_name != $shortName && $degis = Designation::query()->where('short_name', $shortName)->first())
            {
                throw new Exception("Short name {$shortName} already taken!");
            }

            $designation->title = $request->get('title');
            $designation->short_name = $request->get('shortName');
            $designation->description = $request->get('description');
            $designation->summary = $request->get('summary');
            $designation->max_holders = $request->get('maxHolders');
            $designation->probational = $request->get('probational');
            $designation->probation_period = $request->get('probationPeriod');
            $designation->supervisor_id = $request->get('supervisorId');
            $designation->heads = $heads;
            $designation->heads_id = $headsId;

            if ($directorate_id)
            {
                $designation->directorate_id = $directorate_id;
            }

            if ($department_id)
            {
                $designation->department_id = $department_id;
            }

            if ($division_id)
            {
                $designation->division_id = $division_id;
            }

            if ($section_id)
            {
                $designation->section_id = $section_id;
            }

            if ($salary_scale_id = $request->get('salaryScaleId'))
            {
                $designation->salary_scale_id = $salary_scale_id;
            }
            $designation->save();
            return response()->json('Designation Updated!');
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
