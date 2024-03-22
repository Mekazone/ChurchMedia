<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BishopsDeskAttachment extends Model
{
    protected $fillable = ['filePosition', 'name', 'fileCategoryId', 'slug'];
}
