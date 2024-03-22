<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Clergy extends Model
{
    protected $fillable = ['title', 'body', 'slug'];
}
