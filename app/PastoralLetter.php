<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PastoralLetter extends Model
{
    protected $fillable = ['date', 'title', 'body', 'slug', 'category'];
}
