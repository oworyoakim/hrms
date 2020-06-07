<?php

namespace App\Http\Controllers;

use App\Models\LeavePolicy;
use App\Models\LeavePolicyView;
use App\Models\PolicyScale;
use App\Models\SalaryScale;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Exception;
use Illuminate\Support\Facades\DB;
use stdClass;

class LeavePoliciesController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $leave_type_id = $request->get('leaveTypeId');
            if (!$leave_type_id)
            {
                throw new Exception('Leave type required!');
            }
            $policies = LeavePolicy::query()->where('leave_type_id', $leave_type_id)
                                   ->get()
                                   ->map(function (LeavePolicy $policy) {
                                       return $policy->getDetails();
                                   });
            return response()->json($policies);
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function store(Request $request)
    {
        try
        {
            $salaryScales = $request->get('selectedSalaryScaleIds') ?: [];
            $numScales = count($salaryScales);
            if ($numScales == 0)
            {
                throw new Exception('You must select at least one salary scale that this policy applies to!');
            }

            $leave_type_id = $request->get('leaveTypeId');
            if (!$leave_type_id)
            {
                throw new Exception('Leave type required!');
            }

            // validate gender and leave type combination
            $gender = $request->get('gender') ?: 'both';
            $active = !!$request->get('active');

            $policyBuilder = LeavePolicy::active()->where('leave_type_id', $leave_type_id);

            if ($gender == 'male' || $gender == 'female')
            {
                $policyBuilder->where(function ($query) use ($gender) {
                    $query->where('gender', $gender)->orWhere('gender', 'both');
                });
            } else
            {
                $policyBuilder->where('gender', $gender);
            }
            $activePolicies = $policyBuilder->get();
            if ($activePolicies->count())
            {
                throw new Exception("This leave already has an active policy with gender {$gender}. Deactivate policy or change to a different gender and try again!");
            }

            $data = [
                'leave_type_id' => $leave_type_id,
                'title' => $request->get('title'),
                'gender' => $gender,
                'description' => $request->get('description'),
                'duration' => $request->get('duration'),
                'earned_leave' => !!$request->get('earnedLeave'),
                'active' => $active,
                'with_weekend' => !!$request->get('withWeekend'),
                'carry_forward' => !!$request->get('carryForward'),
                'max_carry_forward_duration' => $request->get('maxCarryForwardDuration'),
                'created_by' => $request->get('userId'),
            ];

            $messages = Collection::make();

            // we must validate
            foreach ($salaryScales as $salaryScale)
            {
                foreach ($activePolicies as $activePolicy)
                {
                    $numEntries = PolicyScale::active()
                                             ->where('leave_policy_id', $activePolicy->id)
                                             ->where('salary_scale_id', $salaryScale)
                                             ->count();
                    $scale = SalaryScale::find($salaryScale);
                    if ($numEntries)
                    {
                        $messages->push("This leave already has an active policy with gender {$gender} and salary scale {$scale->scale}. Deactivate policy or change to a different gender and/or salary scale and try again!");
                    }
                }
            }

            if ($messages->count())
            {
                $message = "<ul>";
                foreach ($messages as $msg)
                {
                    $message .= "<li>{$msg}</li>";
                }
                $message .= "</ul>";

                throw new Exception($message);
            }
            // create if we dont have active policies for this leave type
            DB::beginTransaction();
            $policy = LeavePolicy::query()->create($data);
            $count = $policy->addPolicyScales($salaryScales);
            DB::commit();
            $message = "{$count} Records Saved!";
            return response()->json($message);
        } catch (Exception $ex)
        {
            DB::rollback();
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function update(Request $request)
    {
        try
        {
            $leave_policy_id = $request->get('id');
            if (!$leave_policy_id)
            {
                throw new Exception('Leave policy required!');
            }
            $leavePolicy = LeavePolicy::query()->find($leave_policy_id);
            if (!$leavePolicy)
            {
                throw new Exception('Leave policy not found!');
            }
            $leavePolicy->gender = $request->get('gender');
            $leavePolicy->description = $request->get('description');
            $leavePolicy->duration = $request->get('duration');
            $leavePolicy->earned_leave = !!$request->get('earnedLeave');
            $leavePolicy->with_weekend = !!$request->get('withWeekend');
            $leavePolicy->carry_forward = !!$request->get('carryForward');
            $leavePolicy->max_carry_forward_duration = $request->get('maxCarryForwardDuration');
            $leavePolicy->save();

            return response()->json('Record Saved!');
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function delete(Request $request)
    {
        try
        {
            $leave_policy_id = $request->get('leavePolicyId');
            if (!$leave_policy_id)
            {
                throw new Exception('Leave policy required!');
            }
            $leavePolicy = LeavePolicy::find($leave_policy_id);
            if (!$leavePolicy)
            {
                throw new Exception('Leave policy not found!');
            }
            PolicyScale::query()->where('leave_policy_id', $leave_policy_id)->delete();
            $leavePolicy->delete();
            return response()->json('Changes Applied!');
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function activate(Request $request)
    {
        try
        {
            $leave_policy_id = $request->get('leavePolicyId');
            if (!$leave_policy_id)
            {
                throw new Exception('Leave policy required!');
            }
            $leavePolicy = LeavePolicy::find($leave_policy_id);
            if (!$leavePolicy)
            {
                throw new Exception('Leave policy not found!');
            }
            $policyScaleIds = $leavePolicy->scales()->pluck('salary_scale_id')->all();
            $numActivePolicies = LeavePolicyView::query()
                                                ->where('policy_id','<>',$leave_policy_id)
                                                ->where('leave_type_id',$leavePolicy->leave_type_id)
                                                ->where('gender',$leavePolicy->gender)
                                                ->whereIn('salary_scale_id',$policyScaleIds)
                                                ->where('status',true)
                                                ->count();
            if($numActivePolicies){
                throw new Exception("Sorry. There exists at least one active policy with similar settings!");
            }
            DB::beginTransaction();
            PolicyScale::where('leave_policy_id', $leave_policy_id)->update([
                'active' => true,
            ]);
            $leavePolicy->active = true;
            $leavePolicy->save();
            DB::commit();
            return response()->json('Changes Applied!');
        } catch (Exception $ex)
        {
            DB::rollback();
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function deactivate(Request $request)
    {
        try
        {
            $leave_policy_id = $request->get('leavePolicyId');
            if (!$leave_policy_id)
            {
                throw new Exception('Leave policy required!');
            }
            $leavePolicy = LeavePolicy::find($leave_policy_id);
            if (!$leavePolicy)
            {
                throw new Exception('Leave policy not found!');
            }


            DB::beginTransaction();
            PolicyScale::where('leave_policy_id', $leave_policy_id)->update([
                'active' => false,
            ]);
            $leavePolicy->active = false;
            $leavePolicy->save();
            DB::commit();
            return response()->json('Changes Applied!');
        } catch (Exception $ex)
        {
            DB::rollback();
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

}
