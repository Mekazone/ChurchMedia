<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = ['date', 'title', 'body', 'slug', 'category'];
}
