<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TwoFactorCodes extends Model
{
  protected $fillable = [
    'user_id',
    'code',
    'expires_at',
    'attempts'
  ];

  protected $casts = [
    'expires_at' => 'datetime',
  ];
}
