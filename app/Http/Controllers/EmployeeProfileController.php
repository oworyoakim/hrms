<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Employee;
use App\Models\Relationship;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Exception;
use Illuminate\Support\Facades\DB;

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

            $employee = Employee::query()->where('username', $username)->first();

            if (!$employee)
            {
                throw new Exception("Employee {$username} not found!");
            }

            $contacts = $employee->contacts()
                                 ->where('type', 'personal')
                                 ->get()
                                 ->map(function (Contact $contact) {
                                     return $contact->getDetails();
                                 });



            $sonRelationship = Relationship::whereSlug('son')->first();
            $daughterRelationship = Relationship::whereSlug('daughter')->first();
            if ($sonRelationship)
            {
                $numSons = $employee->relatedPersons()
                                    ->where('relationship_id', $sonRelationship->id)
                                    ->count();
            } else
            {
                $numSons = 0;
            }

            if ($daughterRelationship)
            {
                $numDaughters = $employee->relatedPersons()
                                         ->where('relationship_id', $daughterRelationship->id)
                                         ->count();
            } else
            {
                $numDaughters = 0;
            }


            $employee = $employee->getDetails();
            $employee->numSons = $numSons;
            $employee->numDaughters = $numDaughters;
            $employee->numChildren = $numSons + $numDaughters;
            $employee->contacts = $contacts;

            $employee->personalStatement = "Lorem ipsum, dolor sit amet consectetur adipisicing elit. Iste qui repudiandae praesentium quo placeat officia impedit est saepe sunt, ad earum quidem esse voluptates tenetur error quasi sapiente quisquam sequi. Lorem, ipsum dolor sit amet consectetur adipisicing elit. Assumenda sit, quaerat, id deleniti quod amet asperiores perspiciatis earum sed commodi optio sequi nisi ut unde nostrum et harum voluptatibus facilis.";


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
            //$employee->email = $email;

            $employee->nin = $request->get('nin');
            $employee->passport = $request->get('passport');
            $employee->gender = $request->get('gender');
            $employee->marital_status = $request->get('maritalStatus');
            $employee->religion = $request->get('religion');

            if ($avatar = $request->get('avatar'))
            {
                $employee->avatar = $avatar;
            }

            DB::beginTransaction();
            $employee->save();

            $contact = $employee->contacts()
                                ->where('type', 'personal')
                                ->where('mobile', $mobile)
                                ->first();
            if (empty($contact))
            {
                $employee->contacts()->save(new Contact([
                    'mobile' => $mobile,
                ]));
            }
            /*
            else
            {
                $contact = $employee->contacts()
                                    ->where('type', 'personal')
                                    ->where('email', $email)
                                    ->first();
                if (!empty($email) && !empty($contact))
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
            }
            */
            DB::commit();
            return response()->json('Profile updated!');
        } catch (Exception $ex)
        {
            DB::rollBack();
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function updatePhoto(Request $request)
    {
        try
        {
            $username = $request->get('username');
            if (!$username)
            {
                throw new Exception("Username is missing!");
            }
            $avatar = $request->get('avatar');
            if (empty($avatar))
            {
                throw new Exception("Profile photo is missing!");
            }
            $id = $request->get('id');
            $employee = Employee::query()->find($id);
            if (!$employee)
            {
                throw new Exception('Employee not found!');
            }

            $employee->avatar = $avatar;
            DB::beginTransaction();
            $employee->save();
            DB::commit();
            return response()->json('Profile updated!');
        } catch (Exception $ex)
        {
            DB::rollBack();
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
