<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Saint extends Model
{
    protected $fillable = ['date', 'title', 'body', 'slug', 'category'];
}
