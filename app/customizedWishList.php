<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class customizedWishList extends Model
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
