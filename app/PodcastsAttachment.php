<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PodcastsAttachment extends Model
{
    protected $fillable = ['filePosition', 'name', 'fileCategoryId', 'slug', 'postId'];
}
