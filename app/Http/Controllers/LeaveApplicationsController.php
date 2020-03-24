<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\LeaveApplication;
use App\Models\LeaveApplicationSetting;
use App\Models\LeaveTracker;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Exception;
use stdClass;

class LeaveApplicationsController extends Controller
{

    public function index(Request $request)
    {
        try
        {
            $builder = LeaveApplication::with(['employee']);
            $employeeId = $request->get('employee_id');
            $leaveTypeId = $request->get('leave_type_id');
            if ($employeeId)
            {
                $builder->where('employee_id', $employeeId);
            }
            if ($leaveTypeId)
            {
                $builder->where('leave_type_id', $leaveTypeId);
            }
            $leaveApplications = $builder->get()->transform(function (LeaveApplication $item) {
                $application = new stdClass();
                $application->id = $item->id;
                $application->duration = $item->duration;
                $application->startDate = $item->start_date->toDateString();
                $application->status = $item->status;
                $application->createdAt = $item->created_at->toDateTimeString();
                $application->updatedAt = $item->updated_at->toDateTimeString();
                $application->deletedAt = ($item->deleted_at) ? $item->deleted_at->toDateTimeString() : null;
                //dd($application);
                $application->employee = null;
                if ($item->employee)
                {
                    $application->employee = new stdClass();
                    $application->employee->id = $item->employee->id;
                    $application->employee->name = $item->employee->fullName();
                }
                $application->leaveType = null;
                if ($item->leaveType)
                {
                    $application->leaveType = new stdClass();
                    $application->leaveType->id = $item->leaveType->id;
                    $application->leaveType->title = $item->leaveType->title;
                }

                $application->nextActor = null;

                if (in_array($item->status, ['pending', 'verified', 'approved']) && $application->employee && $designationId = $item->employee->designation_id)
                {
                    if ($applicationSetting = LeaveApplicationSetting::where('designation_id', $designationId)->first())
                    {
                        switch ($item->status)
                        {
                            case 'pending':
                                if ($applicationSetting->verified_by && $designation = Designation::find($applicationSetting->verified_by))
                                {
                                    $application->nextActor = $designation->getHolders()->first();
                                }
                                break;
                            case 'verified':
                                if ($applicationSetting->approved_by && $designation = Designation::find($applicationSetting->approved_by))
                                {
                                    $application->nextActor = $designation->getHolders()->first();
                                }
                                break;
                            case 'approved':
                                if ($applicationSetting->granted_by && $designation = Designation::find($applicationSetting->granted_by))
                                {
                                    $application->nextActor = $designation->getHolders()->first();
                                }
                                break;
                        }
                    }
                }
                return $application;
            });
            return response()->json($leaveApplications);
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function store(Request $request)
    {
        try
        {
            $leaveTypeId = $request->get('leave_type_id');
            $leaveType = LeaveType::query()->find($leaveTypeId);
            if (!$leaveType)
            {
                throw new Exception('Leave type not found!');
            }

            $employeeId = $request->get('employee_id');
            $employee = Employee::query()->find($employeeId);
            if (!$employee)
            {
                throw new Exception('Employee not found!');
            }
            $duration = $request->get('duration');
            $startDate = Carbon::parse($request->get('start_date'));
            $endDate = $startDate->clone()->addDays($duration);

            $employee->checkIfCanApplyFor($leaveType, $startDate, $duration);

            $data = [
                'employee_id' => $employeeId,
                'leave_type_id' => $leaveTypeId,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'duration' => $duration,
            ];

            $leaveApplication = LeaveApplication::query()->create($data);

            if ($comment = $request->get('comment'))
            {
                $leaveApplication->comments()->save(new Comment([
                    'user_id' => $employeeId,
                    'body' => $comment
                ]));
            }
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
            $user_id = $request->get('user_id');
            $id = $request->get('id');
            $employee_id = $request->get('employee_id');
            $leave_type_id = $request->get('leave_type_id');
            $start_date = Carbon::parse($request->get('start_date'));
            $duration = $request->get('duration');
            $leaveApplication = LeaveApplication::query()->find($id);
            if (!$leaveApplication)
            {
                throw new Exception("Leave application not found!");
            }
            $leaveApplication->employee_id = $employee_id;
            $leaveApplication->leave_type_id = $leave_type_id;
            $leaveApplication->start_date = $start_date;
            $leaveApplication->duration = $duration;
            $leaveApplication->save();
            if ($comment = $request->get('comment'))
            {
                $leaveApplication->comments()->save(new Comment([
                    'user_id' => $user_id,
                    'body' => $comment
                ]));
            }
            return response()->json('Changes Applied!');
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function verify(Request $request)
    {
        try
        {
            $id = $request->get('leave_application_id');
            $leaveApplication = LeaveApplication::pending()->find($id);
            if (!$leaveApplication)
            {
                throw new Exception('Leave Application not found!');
            }
            if (!$leaveApplication->start_date->greaterThan(Carbon::today()))
            {
                Artisan::queue('leave_application:expire', ['leave_application_id', $leaveApplication->id]);
                throw new Exception('Leave Application has Expired!');
            }

            $leaveApplication->status = 'verified';
            $leaveApplication->save();
            if ($comment = $request->get('comment'))
            {
                $leaveApplication->comments()->save(new Comment([
                    'user_id' => $request->get('user_id'),
                    'body' => $comment
                ]));
            }
            return response()->json('Changes Applied!');
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function returnApplication(Request $request)
    {
        try
        {
            $id = $request->get('leave_application_id');
            $leaveApplication = LeaveApplication::pending()->find($id);
            if (!$leaveApplication)
            {
                throw new Exception('Leave Application not found!');
            }
            if (!$leaveApplication->start_date->greaterThan(Carbon::today()))
            {
                Artisan::queue('leave_application:expire', ['leave_application_id', $leaveApplication->id]);
                throw new Exception('Leave Application has Expired!');
            }

            $leaveApplication->status = 'returned';
            $leaveApplication->save();
            if ($comment = $request->get('comment'))
            {
                $leaveApplication->comments()->save(new Comment([
                    'user_id' => $request->get('user_id'),
                    'body' => $comment
                ]));
            }
            return response()->json('Changes Applied!');
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function approve(Request $request)
    {
        try
        {
            $id = $request->get('leave_application_id');
            $leaveApplication = LeaveApplication::verified()->find($id);
            if (!$leaveApplication)
            {
                throw new Exception('Leave Application not found!');
            }
            if (!$leaveApplication->start_date->greaterThan(Carbon::today()))
            {
                Artisan::queue('leave_application:expire', ['leave_application_id', $leaveApplication->id]);
                throw new Exception('Leave Application has Expired!');
            }

            $leaveApplication->status = 'approved';
            $leaveApplication->save();
            if ($comment = $request->get('comment'))
            {
                $leaveApplication->comments()->save(new Comment([
                    'user_id' => $request->get('user_id'),
                    'body' => $comment
                ]));
            }
            return response()->json('Changes Applied!');
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function decline(Request $request)
    {
        try
        {
            $id = $request->get('leave_application_id');
            $leaveApplication = LeaveApplication::verified()->find($id);
            if (!$leaveApplication)
            {
                throw new Exception('Leave Application not found!');
            }
            if (!$leaveApplication->start_date->greaterThan(Carbon::today()))
            {
                Artisan::queue('leave_application:expire', ['leave_application_id', $leaveApplication->id]);
                throw new Exception('Leave Application has Expired!');
            }

            $leaveApplication->status = 'declined';
            $leaveApplication->save();
            if ($comment = $request->get('comment'))
            {
                $leaveApplication->comments()->save(new Comment([
                    'user_id' => $request->get('user_id'),
                    'body' => $comment
                ]));
            }
            return response()->json('Changes Applied!');
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function grant(Request $request)
    {
        try
        {
            $user_id = $request->get('user_id');

            $id = $request->get('leave_application_id');
            $leaveApplication = LeaveApplication::approved()->find($id);
            if (!$leaveApplication)
            {
                throw new Exception('Leave Application not found!');
            }
            $employee = Employee::find($leaveApplication->employee_id);
            if (!$employee)
            {
                throw new Exception('Employee not found!');
            }
            if (!$leaveApplication->start_date->greaterThan(Carbon::today()))
            {
                Artisan::queue('leave_application:expire', ['leave_application_id', $leaveApplication->id]);
                throw new Exception('Leave Application has Expired!');
            }
            DB::beginTransaction();
            $leaveApplication->status = 'granted';
            $leaveApplication->save();
            // create the leave
            $startDate = $leaveApplication->start_date;
            $endDate = $startDate->addDays($leaveApplication->duration);
            $status = 'pending';
            if (Carbon::now()->gt($startDate))
            {
                $status = 'ongoing';
            } elseif (Carbon::now()->gt($endDate))
            {
                $status = 'completed';
            }

            $leave = Leave::query()->create([
                'leave_type_id' => $leaveApplication->leave_type_id,
                'employee_id' => $leaveApplication->employee_id,
                'leave_application_id' => $leaveApplication->id,
                'start_date' => $leaveApplication->start_date,
                'end_date' => $endDate,
                'status' => $status,
                'user_id' => $user_id,
            ]);

            CarbonPeriod::create($startDate, $endDate)->forEach(function (CarbonInterface $date) use ($leaveApplication, $employee) {
                $lastWorkAnniversary = $employee->lastWorkAnniversary();
                $nextWorkAnniversary = $employee->nextWorkAnniversary();
                if ($lastWorkAnniversary && $nextWorkAnniversary)
                {
                    LeaveTracker::query()->create([
                        'employee_id' => $leaveApplication->employee_id,
                        'leave_type_id' => $leaveApplication->leave_type_id,
                        'date_on_leave' => $date->toDateString(),
                        'status' => 'onleave',
                        'period_start_date' => $lastWorkAnniversary,
                        'period_end_date' => $nextWorkAnniversary->subDays(1),
                    ]);
                }
            });

            if ($comment = $request->get('comment'))
            {
                $leave->comments()->save([
                    'user_id' => $user_id,
                    'body' => $comment
                ]);

                $leaveApplication->comments()->save(new Comment([
                    'user_id' => $user_id,
                    'body' => $comment
                ]));
            }
            DB::commit();
            return response()->json('Changes Applied!');
        } catch (Exception $ex)
        {
            DB::rollback();
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function reject(Request $request)
    {
        try
        {
            $id = $request->get('leave_application_id');
            $leaveApplication = LeaveApplication::approved()->find($id);
            if (!$leaveApplication)
            {
                throw new Exception('Leave Application not found!');
            }
            if (!$leaveApplication->start_date->greaterThan(Carbon::today()))
            {
                Artisan::queue('leave_application:expire', ['leave_application_id', $leaveApplication->id]);
                throw new Exception('Leave Application has Expired!');
            }

            $leaveApplication->status = 'rejected';
            $leaveApplication->save();
            if ($comment = $request->get('comment'))
            {
                $leaveApplication->comments()->save(new Comment([
                    'user_id' => $request->get('user_id'),
                    'body' => $comment
                ]));
            }
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
            $id = $request->get('leave_application_id');
            $leaveApplication = LeaveApplication::pending()->find($id);
            if (!$leaveApplication)
            {
                throw new Exception('Leave application not found!');
            }
            $leaveApplication->delete();
            return response()->json('Changes Applied!');
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

}
