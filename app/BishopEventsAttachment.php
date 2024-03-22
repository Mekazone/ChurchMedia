<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BishopEventsAttachment extends Model
{
    protected $fillable = ['filePosition', 'name', 'fileCategoryId', 'slug', 'postId'];
}
