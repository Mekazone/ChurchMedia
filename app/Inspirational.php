<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inspirational extends Model
{
    protected $fillable = ['date', 'title', 'body', 'slug', 'category'];
}
