<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class customizedDecklist extends Model
{
  protected $casts = [
        'cards' => 'array'
    ];

  protected $fillable = [
      'name',
      'cards',
      'price'
  ];
}
