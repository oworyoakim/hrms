<?php

namespace App\Models;

use stdClass;

class Relationship extends Model
{
    protected $table = 'relationships';

    public function getDetails(){
        $relationship = new stdClass();
        $relationship->id = $this->id;
        $relationship->slug = $this->slug;
        $relationship->title = $this->title;
        return $relationship;
    }
}
