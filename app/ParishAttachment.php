<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ParishAttachment extends Model
{
    protected $fillable = ['filePosition', 'name', 'fileCategoryId', 'slug'];
}
