<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscriptions extends Model
{
	protected $guarded = [];
	
	public function user()
	{
		return $this->belongsTo(User::class)->first();
	}

	public function plan()
	{
		return $this->hasOne(Plans::class, 'plan_id', 'stripe_price');
	}

	public function getPlanAttribute()
	{
		$relation = $this->getRelationValue('plan');
		if ($relation) {
			return $relation;
		}

		$cleanId = preg_replace('/_(month|year).*$/', '', $this->stripe_price);
		return Plans::where('plan_id', $this->stripe_price)
			->orWhere('plan_id', $cleanId)
			->first();
	}

	public function invoice()
	{
		return $this->hasOne(Invoices::class)->whereStatus('paid')->first();
	}
}
