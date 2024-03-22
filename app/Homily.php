<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Homily extends Model
{
    protected $fillable = ['date', 'title', 'body', 'slug', 'category'];
}
