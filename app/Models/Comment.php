<?php
namespace App\Models;

use Illuminate\Support\Carbon;

/**
 * @property int id
 * @property string body
 * @property  int user_id
 * @property  int commentable_id
 * @property  string commentable_type
 * @property  Carbon created_at
 * @property  Carbon updated_at
 */
class Comment extends Model
{
    protected $table = 'comments';

    public function commentable()
    {
        return $this->morphTo();
    }
}
