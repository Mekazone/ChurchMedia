<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PastoralLettersAttachment extends Model
{
    protected $fillable = ['filePosition', 'name', 'fileCategoryId', 'slug', 'postId'];
}
