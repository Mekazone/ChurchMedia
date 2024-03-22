<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClergyAttachment extends Model
{
    protected $fillable = ['filePosition', 'name', 'fileCategoryId', 'slug'];
}
