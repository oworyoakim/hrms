<?php
/**
 * Created by PhpStorm.
 * User: Yoakim
 * Date: 10/27/2018
 * Time: 8:25 AM
 */

namespace App\Traits;

use App\Models\Contact;

trait Contactable
{
    public function contacts()
    {
        return $this->morphMany(Contact::class, 'contactable');
    }
}
