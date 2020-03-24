<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Employee;
use App\Models\Relationship;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Exception;

class EmployeeProfileController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $username = $request->get('username');
            if (!$username)
            {
                throw new Exception("Username is missing!");
            }

            $employee = Employee::with([
                'designation',
                'department',
                'directorate'
            ])->where('username', $username)->first();

            if (!$employee)
            {
                throw new Exception("Employee {$username} not found!");
            }

            $employee->personalStatement = "Lorem ipsum, dolor sit amet consectetur adipisicing elit. Iste qui repudiandae praesentium quo placeat officia impedit est saepe sunt, ad earum quidem esse voluptates tenetur error quasi sapiente quisquam sequi. Lorem, ipsum dolor sit amet consectetur adipisicing elit. Assumenda sit, quaerat, id deleniti quod amet asperiores perspiciatis earum sed commodi optio sequi nisi ut unde nostrum et harum voluptatibus facilis.";

            $employee->full_name = $employee->fullName();
            $employee->subordinates = $employee->subordinates();

            $contacts = $employee->contacts()
                                 ->where(function ($query) {
                                     $query->whereNotNull('email')
                                           ->orWhereNotNull('mobile');
                                 })
                                 ->where('type', 'personal')
                                 ->get();
            $employee->contacts = $contacts;
            $employee->mobile = $contacts->implode('mobile', ', ');

            $employee->supervisor = $employee->supervisor();

            if ($employee->supervisor)
            {
                $employee->supervisor->full_name = $employee->supervisor->fullName();
            }

            $sonRelationship = Relationship::whereSlug('son')->first();
            $daughterRelationship = Relationship::whereSlug('daughter')->first();
            if ($sonRelationship)
            {
                $employee->number_of_sons = $employee->relatedPersons()
                                                     ->where('relationship_id', $sonRelationship->id)
                                                     ->count();
            } else
            {
                $employee->number_of_sons = 0;
            }

            if ($daughterRelationship)
            {
                $employee->number_of_daughters = $employee->relatedPersons()
                                                          ->where('relationship_id', $daughterRelationship->id)
                                                          ->count();
            } else
            {
                $employee->number_of_daughters = 0;
            }

            $employee->number_of_children = $employee->number_of_sons + $employee->number_of_daughters;

            return response()->json($employee);
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function update(Request $request)
    {
        try
        {
            $username = $request->get('username');
            if (!$username)
            {
                throw new Exception("Username is missing!");
            }
            $employee_id = $request->get('employee_id');
            if (!$employee_id)
            {
                throw new Exception('Employee id is missing!');
            }
            $employee = Employee::query()->find($employee_id);
            if (!$employee)
            {
                throw new Exception('Employee not found!');
            }

            $email = $request->get('email');
            $mobile = $request->get('mobile');

            $employee->nin = $request->get('nin');
            $employee->passport = $request->get('passport');
            // $employee->email = $email;
            $employee->gender = $request->get('gender');
            $employee->marital_status_id = $request->get('marital_status_id');
            $employee->religion_id = $request->get('religion_id');

            if ($avatar = $request->get('avatar'))
            {
                $employee->avatar = $avatar;
            }

            $employee->save();

            if (
                $mobile && $contact = $employee->contacts()
                                               ->where('type', 'personal')
                                               ->where('mobile', $mobile)
                                               ->first()
            )
            {
                $contact->email = $email;
                $contact->save();
            } elseif (
                $email && $contact = $employee->contacts()
                                              ->where('type', 'personal')
                                              ->where('email', $email)
                                              ->first()
            )
            {
                $contact->mobile = $mobile;
                $contact->save();
            } else
            {
                $employee->contacts()->save(new Contact([
                    'mobile' => $mobile,
                    'email' => $email
                ]));
            }

            return response()->json('Profile updated!');
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function download(Request $request)
    {
        try
        {
            $email = $request->get('email');
            $employee = Employee::query()->where('email', $email)->first();
            if (!$employee)
            {
                throw new Exception("Employee not found!");
            }

            $data = [
                'employee' => $employee
            ];
            return response()->json($data);
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
