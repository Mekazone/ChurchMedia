<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LettersAttachment extends Model
{
    protected $fillable = ['filePosition', 'name', 'fileCategoryId', 'slug', 'postId'];
}
