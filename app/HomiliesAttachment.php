<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HomiliesAttachment extends Model
{
    protected $fillable = ['filePosition', 'name', 'fileCategoryId', 'slug', 'postId'];
}
