<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use stdClass;

class DocumentsController extends Controller
{

    public function index(Request $request)
    {
        try
        {
            $employeeId = $request->get('employeeId');
            $builder = Document::query();
            if ($employeeId)
            {
                $builder->where('employee_id', $employeeId);
            }
            $documents = $builder->get()
                                 ->map(function (Document $document) {
                                     return $document->getDetails();
                                 });
            return response()->json($documents);
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function store(Request $request)
    {
        try
        {
            $employeeId = $request->get('employeeId');
            $title = $request->get('title');
            $description = $request->get('description');
            $documentCategoryId = $request->get('documentCategoryId');
            $documentTypeId = $request->get('documentTypeId');
            $path = $request->get('path');
            $type = $request->get('type');
            $size = $request->get('size');

            $rules = [
                'title' => 'required',
                'documentCategoryId' => 'required|numeric',
                'documentTypeId' => 'required|numeric',
                'path' => 'required',
                'type' => 'required',
                'size' => 'required',
            ];
            $this->validateData($request->all(), $rules);
            // create the entry in the db
            $data = [
                'document_category_id' => $documentCategoryId,
                'document_type_id' => $documentTypeId,
            ];

            if (!empty($employeeId))
            {
                $data['employee_id'] = $employeeId;
            }

            Document::query()->updateOrCreate($data, [
                'title' => $title,
                'type' => $type,
                'size' => $size,
                'description' => $description,
                'path' => $path,
            ]);
            return response()->json("Document Uploaded!");
        } catch (Exception $ex)
        {
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
