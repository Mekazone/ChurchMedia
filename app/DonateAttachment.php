<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DonateAttachment extends Model
{
    protected $fillable = ['filePosition', 'name', 'fileCategoryId', 'slug'];
}
