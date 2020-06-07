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
            $leaveTypes = LeaveType::all()
                                   ->map(function (LeaveType $leaveType) {
                                       return $leaveType->getDetails();
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
            $rules = [
                'title' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            $data = [
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'created_by' => $request->get('userId'),
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
            $rules = [
                'id' => 'required',
                'title' => 'required',
                'userId' => 'required',
            ];

            $this->validateData($request->all(), $rules);

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
            $leaveType->updated_by = $request->get('userId');
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
            $leaveTypeId = $request->get('leaveTypeId');
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
            $leaveTypeId = $request->get('leaveTypeId');
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
            $leaveTypeId = $request->get('leaveTypeId');
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
