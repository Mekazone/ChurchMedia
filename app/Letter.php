<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Letter extends Model
{
    protected $fillable = ['date', 'title', 'body', 'slug', 'category'];
}
