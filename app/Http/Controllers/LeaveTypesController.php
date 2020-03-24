<?php

namespace App\Http\Controllers;

use App\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Exception;

class LeaveTypesController extends Controller
{

    public function index(Request $request)
    {
        try
        {
            $leaveTypes = LeaveType::all()->map(function (LeaveType $leaveType) {
                $leaveType->numOngoing = $leaveType->leaves()->ongoing()->count();
                $leaveType->active = !!$leaveType->active;
                return $leaveType;
            });
            return response()->json($leaveTypes);
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
            ];
            $leaveType = LeaveType::query()->create($data);
            Artisan::call('load:leave-balances', ['leave_type_id' => $leaveType->id]);
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
            $title = $request->get('title');
            $description = $request->get('description');
            $leaveType = LeaveType::query()->find($id);
            if (!$leaveType)
            {
                throw new Exception("Leave type not found!");
            }
            $leaveType->title = $title;
            $leaveType->description = $description;
            $leaveType->save();
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
            $leaveTypeId = $request->get('leave_type_id');
            $leaveType = LeaveType::inactive()->find($leaveTypeId);
            if (!$leaveType)
            {
                throw new Exception('Leave type not found!');
            }
            $leaveType->active = true;
            $leaveType->save();
            return response()->json('Changes Applied!');
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function deactivate(Request $request)
    {
        try
        {
            $leaveTypeId = $request->get('leave_type_id');
            $leaveType = LeaveType::active()->find($leaveTypeId);
            if (!$leaveType)
            {
                throw new Exception('Leave type not found!');
            }
            $leaveType->active = false;
            $leaveType->save();
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
            $leaveTypeId = $request->get('leave_type_id');
            $leaveType = LeaveType::find($leaveTypeId);
            if (!$leaveType)
            {
                throw new Exception('Leave type not found!');
            }
            $leaveType->delete();
            return response()->json('Changes Applied!');
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

}
