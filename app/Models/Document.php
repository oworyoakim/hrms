<?php

namespace App\Models;

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
}
