<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OpinionsAttachment extends Model
{
    protected $fillable = ['filePosition', 'name', 'fileCategoryId', 'slug', 'postId'];
}
