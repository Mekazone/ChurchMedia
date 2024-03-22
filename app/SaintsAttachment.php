<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SaintsAttachment extends Model
{
    protected $fillable = ['filePosition', 'name', 'fileCategoryId', 'slug', 'postId'];
}
