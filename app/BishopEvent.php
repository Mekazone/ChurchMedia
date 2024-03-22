<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BishopEvent extends Model
{
    protected $fillable = ['date', 'title', 'body', 'slug', 'category'];
}
