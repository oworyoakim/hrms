<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\RelatedPerson;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Exception;

class RelatedPersonInfoController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $employeeId = $request->get('employeeId');
            if (!$employeeId)
            {
                throw new Exception('Employee required!');
            }
            $persons = RelatedPerson::query()
                                    ->where('employee_id', $employeeId)
                                    ->get()
                                    ->map(function (RelatedPerson $person) {
                                        return $person->getDetails();
                                    });
            return response()->json($persons);
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
                'firstName' => 'required',
                'lastName' => 'required',
                'gender' => 'required',
                'relationshipId' => 'required',
                'employeeId' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            $employeeId = $request->get('employeeId');
            if (!$employeeId)
            {
                throw new Exception('Employee required!');
            }
            $data = [
                'employee_id' => $employeeId,
                'title' => $request->get('title'),
                'first_name' => $request->get('firstName'),
                'last_name' => $request->get('lastName'),
                'middle_name' => $request->get('middleName'),
                'gender' => $request->get('gender'),
                'phone' => $request->get('phone'),
                'email' => $request->get('email'),
                'relationship_id' => $request->get('relationshipId'),
                'dob' => $request->get('dob'),
                'nin' => $request->get('nin'),
                'dependant' => $request->get('dependant'),
                'emergency' => $request->get('emergency'),
                'is_next_of_kin' => $request->get('isNextOfKin'),
                'created_by' => $request->get('userId'),
            ];
            RelatedPerson::query()->create($data);
            return response()->json("Related person info created!");
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
                'firstName' => 'required',
                'lastName' => 'required',
                'gender' => 'required',
                'relationshipId' => 'required',
                'employeeId' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            $id = $request->get('id');
            $employeeId = $request->get('employeeId');
            if (!$employeeId)
            {
                throw new Exception('Employee required!');
            }

            $person = RelatedPerson::query()->where('employee_id', $employeeId)->find($id);

            if (!$person)
            {
                throw new Exception('Person not found!');
            }

            $person->title = $request->get('title');
            $person->first_name = $request->get('firstName');
            $person->last_name = $request->get('lastName');
            $person->middle_name = $request->get('middleName');
            $person->gender = $request->get('gender');
            $person->relationship_id = $request->get('relationshipId');
            $person->dob = $request->get('dob');
            $person->nin = $request->get('nin');
            $person->dependant = $request->get('dependant');
            $person->emergency = $request->get('emergency');
            $person->is_next_of_kin = $request->get('isNextOfKin');
            $person->email = $request->get('email');
            $person->phone = $request->get('phone');
            $person->updated_by = $request->get('userId');
            $person->save();

            return response()->json("Related person info updated!");
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

}
