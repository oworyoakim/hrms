<?php
/**
 * Created by PhpStorm.
 * User: Yoakim
 * Date: 10/27/2018
 * Time: 8:25 AM
 */

namespace App\Traits;

use App\Models\Comment;

trait Commentable
{
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}