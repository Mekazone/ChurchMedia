<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Opinion extends Model
{
    protected $fillable = ['date', 'title', 'body', 'slug', 'category'];
}
