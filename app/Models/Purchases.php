<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchases extends Model
{
  protected $guarded = [];
  const CREATED_AT = 'date';
  const UPDATED_AT = null;

  public function user()
  {
    return $this->belongsTo(User::class)->first();
  }

  public function author()
  {
    return $this->belongsTo(User::class, 'user_id');
  }

  public function images()
  {
    return $this->belongsTo(Images::class);
  }

  public function invoice()
  {
    return $this->hasOne(Invoices::class)->whereStatus('paid');
  }
}
