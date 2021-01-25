<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class customizedCards extends Model
{
    protected $fillable = [
        'email',
        'name',
        'stars',
        'monster-type',
        'attr',
        'card-type',
        'atk',
        'def',
        'img',
        'description'
    ];
  
    protected  $primaryKey = 'id';
}
