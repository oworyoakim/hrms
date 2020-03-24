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
            $employee_id = $request->get('employee_id');
            if (!$employee_id)
            {
                throw new Exception('Employee required!');
            }
            $persons = RelatedPerson::query()
                                    ->where('employee_id', $employee_id)
                                    ->get()
                                    ->map(function (RelatedPerson $person) {
                                        $person->fullName = $person->fullName();
                                        $contacts = $person->contacts()->get();
                                        $person->email = $contacts->implode('email', ', ');
                                        $person->mobile = $contacts->implode('mobile', ', ');
                                        return $person;
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
            $employee_id = $request->get('employee_id');
            if (!$employee_id)
            {
                throw new Exception('Employee required!');
            }
            $data = [
                'employee_id' => $employee_id,
                'title' => $request->get('title'),
                'first_name' => $request->get('first_name'),
                'last_name' => $request->get('last_name'),
                'middle_name' => $request->get('middle_name'),
                'gender' => $request->get('gender'),
                'relationship_id' => $request->get('relationship_id'),
                'dob' => $request->get('dob'),
                'nin' => $request->get('nin'),
                'dependant' => $request->get('dependant'),
                'emergency' => $request->get('emergency'),
                'is_next_of_kin' => $request->get('is_next_of_kin'),
            ];
            $person = RelatedPerson::query()->create($data);
            $email = $request->get('email');
            $mobile = $request->get('mobile');
            if ($mobile || $email)
            {
                $person->contacts()->save(new Contact([
                    'mobile' => $mobile,
                    'email' => $email
                ]));
            }
            return response()->json("Record Saved!");
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
            $employee_id = $request->get('employee_id');
            if (!$employee_id)
            {
                throw new Exception('Employee required!');
            }

            $person = RelatedPerson::query()->where('employee_id', $employee_id)->find($id);

            if (!$person)
            {
                throw new Exception('Person not found!');
            }

            $person->title = $request->get('title');
            $person->first_name = $request->get('first_name');
            $person->last_name = $request->get('last_name');
            $person->middle_name = $request->get('middle_name');
            $person->gender = $request->get('gender');
            $person->relationship_id = $request->get('relationship_id');
            $person->dob = $request->get('dob');
            $person->nin = $request->get('nin');
            $person->dependant = $request->get('dependant');
            $person->emergency = $request->get('emergency');
            $person->is_next_of_kin = $request->get('is_next_of_kin');
            $person->save();

            $email = $request->get('email');
            $mobile = $request->get('mobile');
            if ($mobile && $contact = $person->contacts()->where('type', 'personal')->where('mobile', $mobile)->first())
            {
                $contact->email = $email;
                $contact->save();
            } elseif ($email && $contact = $person->contacts()->where('type', 'personal')->where('email', $email)->first())
            {
                $contact->mobile = $mobile;
                $contact->save();
            } else
            {
                $person->contacts()->save(new Contact([
                    'mobile' => $mobile,
                    'email' => $email
                ]));
            }

            return response()->json("Record Saved!");
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

}
