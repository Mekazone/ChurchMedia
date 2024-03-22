<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InspirationalsAttachment extends Model
{
    protected $fillable = ['filePosition', 'name', 'fileCategoryId', 'slug', 'postId'];
}
