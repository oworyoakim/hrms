<?php

namespace App\Models;

use stdClass;

class Document extends Model
{
    protected $guarded = [];

    public function employee(){
        return $this->belongsTo(Employee::class,'employee_id');
    }
    public function documentCategory(){
        return $this->belongsTo(DocumentCategory::class,'document_category_id');
    }

    public function documentType(){
        return $this->belongsTo(DocumentType::class,'document_type_id');
    }

    public function getDetails(){
        $document = new stdClass();
        $document->id = $this->id;
        $document->title = $this->title;
        $document->description = $this->description;
        $document->size = round($this->size / (1024), 2);
        $document->type = $this->type;
        $document->path = $this->path;
        $document->lastModifiedAt = $this->updated_at->toDateTimeString();
        $document->documentCategoryId = $this->document_category_id;
        $document->documentTypeId = $this->document_type_id;
        $document->employee = null;
        if ($employee = $this->employee)
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
        if ($this->documentCategory)
        {
            $document->documentCategory = new stdClass();
            $document->documentCategory->id = $this->documentCategory->id;
            $document->documentCategory->title = $this->documentCategory->title;
            $document->documentCategory->nonEmployee = !!$this->documentCategory->non_employee;
        }
        $document->documentType = null;
        if ($this->documentType)
        {
            $document->documentType = new stdClass();
            $document->documentType->id = $this->documentType->id;
            $document->documentType->title = $this->documentType->title;
            $document->documentType->categoryId = $this->documentType->category_id;
        }
        return $document;
    }
}
