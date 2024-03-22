<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServicesAttachment extends Model
{
    protected $fillable = ['filePosition', 'name', 'fileCategoryId', 'slug'];
}
