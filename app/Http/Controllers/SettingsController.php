<?php

namespace App\Http\Controllers;

use App\Models\LeaveApplicationSetting;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Exception;

class SettingsController extends Controller
{
    public function updateLeaveApplicationsSettings(Request $request)
    {
        try
        {
            $rules = [
                'designationId' => 'required',
                'verifiedBy' => 'required',
                'approvedBy' => 'required',
                'grantedBy' => 'required',
                'userId' => 'required',
            ];

            $this->validateData($request->all(), $rules);
            $designationId = $request->get('designationId');
            if (!$designationId)
            {
                throw  new Exception('Designation ID is required!');
            }
            $leaveApplicationSetting = LeaveApplicationSetting::query()->firstOrNew([
                'designation_id' => $designationId,
            ]);
            $leaveApplicationSetting->verified_by = $request->get('verifiedBy');
            $leaveApplicationSetting->approved_by = $request->get('approvedBy');
            $leaveApplicationSetting->granted_by = $request->get('grantedBy');
            $leaveApplicationSetting->updated_by = $request->get('userId');
            $leaveApplicationSetting->save();
            return response()->json('Changes Applied!');
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }


}
