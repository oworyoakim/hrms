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
            $employeeId = $request->get('employee_id');
            $builder = Document::query();
            if ($employeeId)
            {
                $builder->where('employee_id', $employeeId);
            }
            $documents = $builder->get()->transform(function (Document $doc) {
                $document = new stdClass();
                $document->id = $doc->id;
                $document->title = $doc->title;
                $document->description = $doc->description;
                $document->size = round($doc->size / (1024), 2);
                $document->type = $doc->type;
                $document->path = $doc->path;
                $document->lastModifiedAt = $doc->updated_at->toDateTimeString();
                $document->documentCategoryId = $doc->document_category_id;
                $document->documentTypeId = $doc->document_type_id;
                $document->employee = null;
                if ($employee = $doc->employee)
                {
                    $document->employee = new stdClass();
                    $document->employee->id = $employee->id;
                    $document->employee->fullName = $employee->fullName();
                    $document->employee->firstName = $employee->first_name;
                    $document->employee->lastName = $employee->last_name;
                    $document->employee->username = $employee->username;
                    $document->employee->avatar = $employee->avatar;
                }
                $document->documentCategory = null;
                if ($doc->documentCategory)
                {
                    $document->documentCategory = new stdClass();
                    $document->documentCategory->id = $doc->documentCategory->id;
                    $document->documentCategory->title = $doc->documentCategory->title;
                    $document->documentCategory->nonEmployee = !!$doc->documentCategory->non_employee;
                }
                $document->documentType = null;
                if ($doc->documentType)
                {
                    $document->documentType = new stdClass();
                    $document->documentType->id = $doc->documentType->id;
                    $document->documentType->title = $doc->documentType->title;
                    $document->documentType->categoryId = $doc->documentType->category_id;
                }
                return $document;
            });
            return response()->json($documents);
        } catch (Exception $ex)
        {
            Log::error("GET_DOCUMENTS: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function store(Request $request)
    {
        try
        {
            //dd($request->all());
            if (!$request->hasFile('path'))
            {
                throw new Exception("No file attached!");
            }
            $employeeId = $request->get('employeeId');
            $employee = Employee::find($employeeId);
            $title = $request->get('title');
            $description = $request->get('description');
            $documentCategoryId = $request->get('documentCategoryId');
            $documentTypeId = $request->get('documentTypeId');
            $employeeDocument = $request->file('path');
            $rules = [
                'title' => 'required',
                'documentCategoryId' => 'required|numeric',
                'documentTypeId' => 'required|numeric',
                'document' => 'required|file'
            ];
            $validator = Validator::make([
                'title' => $title,
                'documentCategoryId' => $documentCategoryId,
                'documentTypeId' => $documentTypeId,
                'document' => $employeeDocument,
            ], $rules);

            if ($validator->fails())
            {
                $errors = "";
                foreach ($validator->errors()->messages() as $key => $messages)
                {
                    $errors .= "<p class='text-small'>" . ucfirst($key) . ": " . implode('<br/>', $messages) . "</p>";
                }
                throw new Exception("Validation Error: {$errors}");
            }

            $ext = $employeeDocument->clientExtension();
            $size = $employeeDocument->getSize();
            $maxSize = 1024 * 1024;
            if ($size > $maxSize)
            {
                throw new Exception("Your file is larger than  1MB.");
            }
            if (!in_array($ext, ['mimes', 'jpeg', 'jpg', 'bmp', 'png', 'pdf']))
            {
                throw new Exception("The allowed file formats are: mimes,jpeg,jpg,bmp,png,pdf.");
            }
            // upload the document
            if ($employee)
            {
                $relativePath = "uploads/employee/" . str_replace('/', '_', $employee->employee_number);
            } else
            {
                $relativePath = "uploads/non-employee";
            }
            $fileName = Str::slug($title) . ".{$ext}";
            Storage::disk('local')->putFileAs("public/{$relativePath}", $employeeDocument, $fileName);
            // create the entry in the db
            $data = [
                'document_category_id' => $documentCategoryId,
                'document_type_id' => $documentTypeId,
            ];
            if ($employee)
            {
                $data['employee_id'] = $employeeId;
            }
            Document::query()->updateOrCreate($data, [
                'title' => $title,
                'type' => $ext,
                'size' => $size,
                'description' => $description,
                'path' => "/storage/{$relativePath}/{$fileName}",
            ]);
            return response()->json("Document Uploaded!");
        } catch (Exception $ex)
        {
            Log::error("GET_DOCUMENTS: {$ex->getMessage()}");
            return response()->json($ex->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }
}
