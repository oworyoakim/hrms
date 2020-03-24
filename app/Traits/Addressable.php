<?php
/**
 * Created by PhpStorm.
 * User: Yoakim
 * Date: 10/27/2018
 * Time: 8:25 AM
 */

namespace App\Traits;

use App\Models\Address;

trait Addressable
{
    public function addresses()
    {
        return $this->morphMany(Address::class, 'addressable');
    }
}
