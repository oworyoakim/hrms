<?php

namespace App\Models;

use stdClass;

class Contact extends Model
{
    public function contactable()
    {
        return $this->morphTo();
    }

    public function getDetails(){
        $contact = new stdClass();
        $contact->id = $this->id;
        $contact->mobile = $this->mobile;
        $contact->email = $this->email;
        $contact->extension = $this->extension;
        $contact->fax = $this->fax;
        $contact->type = $this->type;
        return $contact;
    }
}
