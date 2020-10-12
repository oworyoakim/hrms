<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Exception;

class ContactsController extends Controller
{

    public function store(Request $request)
    {
        try
        {
            $rules = [
                'value' => 'required',
                'kind' => 'required',
                'type' => 'required',
                'contactable' => 'required',
                'contactableId' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);

            $contactableId = $request->get('contactableId');
            $contactable = $request->get('contactable');

            $data = [
                'value' => $request->get('value'),
                'kind' => $request->get('kind'),
                'type' => $request->get('type'),
                'contactable_type' => "App\Models\\{$contactable}",
                'contactable_id' => $contactableId,
                'created_by' => $request->get('userId'),
            ];
            Contact::query()->create($data);
            return response()->json('Contact created!');
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
                'value' => 'required',
                'kind' => 'required',
                'type' => 'required',
                'userId' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            $id = $request->get('id');
            $contact = Contact::query()->find($id);
            if (!$contact)
            {
                throw new Exception('Contact not found!');
            }
            $contact->value = $request->get('value');
            $contact->kind = $request->get('kind');
            $contact->type = $request->get('type');
            $contact->updated_by = $request->get('userId');
            $contact->save();
            return response()->json('Contact updated!');
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function delete(Request $request)
    {
        try
        {
            $id = $request->get('contactId');
            $contact = Contact::query()->find($id);
            if (!$contact)
            {
                throw new Exception("Contact not found!");
            }
            $contact->delete();
            return response()->json('Contact deleted!');
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
