<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Designation;
use App\Models\Directorate;
use App\Models\Division;
use App\Models\LeaveApplicationSetting;
use App\Models\Section;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class DesignationsController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $builder = Designation::with(['department', 'directorate', 'salaryScale', 'section']);
            $scope = $request->get('scope');
            if ($scope == 'executive-secretary')
            {
                $builder->forExecutiveSecretary();
            }
             else
            {
                //$builder->forDirectorate();
                if ($directorate_id = $request->get('directorate_id'))
                {
                    $builder->where('directorate_id', $directorate_id);
                }
            }
            if ($department_id = $request->get('department_id'))
            {
                $builder->where('department_id', $department_id);
            }

            if ($division_id = $request->get('division_id'))
            {
                $builder->where('division_id', $division_id);
            }

            if ($salary_scale_id = $request->get('salary_scale_id'))
            {
                $builder->where('salary_scale_id', $salary_scale_id);
            }

            if ($section_id = $request->get('section_id'))
            {
                $builder->where('section_id', $section_id);
            }
            $designations = $builder->get()->transform(function ($designation){
                $leaveApplicationSetting = LeaveApplicationSetting::where('designation_id',$designation->id)->first();
                if($leaveApplicationSetting){
                    if($leaveApplicationSetting->verified_by){
                        $designation->leaveApplicationVerifier = Designation::find($leaveApplicationSetting->verified_by);
                    }else{
                        $designation->leaveApplicationVerifier = null;
                    }
                    if($leaveApplicationSetting->approved_by){
                        $designation->leaveApplicationApprover = Designation::find($leaveApplicationSetting->approved_by);
                    }else{
                        $designation->leaveApplicationApprover = null;
                    }
                    if($leaveApplicationSetting->granted_by){
                        $designation->leaveApplicationGranter = Designation::find($leaveApplicationSetting->granted_by);
                    }else{
                        $designation->leaveApplicationGranter = null;
                    }
                }else{
                    $designation->leaveApplicationVerifier = null;
                    $designation->leaveApplicationApprover = null;
                    $designation->leaveApplicationGranter = null;
                }
                return $designation;
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

            $data = [
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'summary' => $request->get('summary'),
                'salary_scale_id' => $request->get('salary_scale_id'),
                'section_id' => $request->get('section_id'),
                'max_holders' => $request->get('max_holders'),
                'probational' => $request->get('probational'),
                'probation_period' => $request->get('probation_period'),
                'supervisor_id' => $request->get('supervisor_id'),
            ];

            $designation = new Designation($data);

            // section level
            $section_id = $request->get('section_id');
            if ($section_id && $section = Section::find($section_id))
            {
                $designation->company_id = $section->company_id;
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
            $division_id = $request->get('division_id');
            if ($division_id && $division = Division::find($division_id))
            {
                if (!$designation->company_id)
                {
                    $designation->company_id = $division->company_id;
                }
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
            $department_id = $request->get('department_id');
            if ($department_id && $department = Department::find($department_id))
            {
                if (!$designation->company_id)
                {
                    $designation->company_id = $department->company_id;
                }
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
            $directorate_id = $request->get('directorate_id');
            if ($directorate_id && $directorate = Directorate::find($directorate_id))
            {
                if (!$designation->company_id)
                {
                    $designation->company_id = $directorate->company_id;
                }
                if (!$designation->directorate_id)
                {
                    $designation->directorate_id = $directorate->id;
                } elseif ($designation->directorate_id != $directorate_id)
                {
                    throw new Exception('The selected department, division, or section does not belong to the selected directorate');
                }
            }

            if (!$designation->company_id)
            {
                $designation->company_id = $request->attributes->get('company_id');
            }

            DB::beginTransaction();

            $designation->save();

            $designation->refresh();

            LeaveApplicationSetting::query()->create([
                'designation_id' => $designation->id,
                'verified_by' => $request->get('default_leave_application_verifier'),
                'approved_by' => $designation->supervisor_id,
                'granted_by' => $request->get('default_leave_application_granter'),
            ]);

            DB::commit();

            return response()->json('Record Saved!');

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
            if (!$id)
            {
                throw new Exception('Designation required!');
            }
            $designation = Designation::find($id);
            if (!$designation)
            {
                throw new Exception('Designation not found!');
            }

            $designation->title = $request->get('title');
            $designation->description = $request->get('description');
            $designation->summary = $request->get('summary');
            $designation->max_holders = $request->get('max_holders');
            $designation->probational = $request->get('probational');
            $designation->probation_period = $request->get('probation_period');
            $designation->supervisor_id = $request->get('supervisor_id');

            if($directorate_id = $request->get('directorate_id'))
            {
                $designation->directorate_id = $directorate_id;
            }

            if($department_id = $request->get('department_id'))
            {
                $designation->department_id = $department_id;
            }

            if($division_id = $request->get('division_id'))
            {
                $designation->division_id = $division_id;
            }

            if($section_id = $request->get('section_id'))
            {
                $designation->section_id = $section_id;
            }

            if($salary_scale_id = $request->get('salary_scale_id'))
            {
                $designation->salary_scale_id = $salary_scale_id;
            }
            $designation->save();
            return response()->json('Changes Applied!');
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
