<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NewsAttachment extends Model
{
    protected $fillable = ['filePosition', 'name', 'fileCategoryId', 'slug', 'postId'];
}
