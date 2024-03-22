<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AboutUsAttachment extends Model
{
    protected $fillable = ['filePosition', 'name', 'fileCategoryId', 'slug'];
}
