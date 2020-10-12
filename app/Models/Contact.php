<?php

namespace App\Models;

use stdClass;

/**
 * Class Contact
 * @package App\Models
 * @property int id
 * @property string value
 * @property string kind
 * @property string type
 */
class Contact extends Model
{
    public function contactable()
    {
        return $this->morphTo();
    }

    public function getDetails(){
        $contact = new stdClass();
        $contact->id = $this->id;
        $contact->kind = $this->kind;
        $contact->type = $this->type;
        $contact->value = $this->value;
        return $contact;
    }
}
