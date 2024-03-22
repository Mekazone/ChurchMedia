<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VideosAttachment extends Model
{
    protected $fillable = ['filePosition', 'name', 'fileCategoryId', 'slug', 'postId'];
}
