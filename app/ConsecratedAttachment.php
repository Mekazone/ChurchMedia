<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ConsecratedAttachment extends Model
{
    protected $fillable = ['filePosition', 'name', 'fileCategoryId', 'slug'];
}
