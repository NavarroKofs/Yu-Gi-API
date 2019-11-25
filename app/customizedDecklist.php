<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class customizedDecklist extends Model
{
  protected $casts = [
        'cards' => 'array',
        'illegalCards' => 'array'
    ];


  protected $fillable = [
      'name',
      'cards',
      'legality',
      'size',
      'illegalCards'
  ];

    protected  $primaryKey = 'name';
}
