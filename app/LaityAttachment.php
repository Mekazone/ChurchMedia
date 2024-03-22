<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LaityAttachment extends Model
{
    protected $fillable = ['filePosition', 'name', 'fileCategoryId', 'slug'];
}
