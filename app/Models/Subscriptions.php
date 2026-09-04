<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscriptions extends Model
{
	protected $guarded = [];
	
	public function user()
	{
		return $this->belongsTo(User::class, 'user_id');
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
		return $this->hasOne(Invoices::class, 'subscriptions_id')->whereStatus('paid');
	}

	public function getStatusDetailAttribute()
	{
		$endsAt = $this->ends_at ? \Carbon\Carbon::parse($this->ends_at) : null;

		if (!$endsAt) {
			return [
				'code' => 'active',
				'label' => __('misc.active'),
				'badge' => 'bg-success',
				'text_color' => 'text-success'
			];
		}

		// Expired
		if ($endsAt->isPast()) {
			if ($this->cancelled === 'yes') {
				return [
					'code' => 'cancelled_expired',
					'label' => 'Cancelled & Expired',
					'badge' => 'bg-secondary',
					'text_color' => 'text-muted'
				];
			}
			return [
				'code' => 'expired',
				'label' => __('misc.expired'),
				'badge' => 'bg-danger',
				'text_color' => 'text-danger'
			];
		}

		// Cancelled but still in grace period (ends_at in future)
		if ($this->cancelled === 'yes') {
			return [
				'code' => 'cancelled_grace',
				'label' => 'Cancelled (Grace Period)',
				'badge' => 'bg-warning text-dark',
				'text_color' => 'text-warning'
			];
		}

		// Expiring Soon (within 3 days)
		if ($endsAt->diffInDays(now()) <= 3) {
			return [
				'code' => 'expiring_soon',
				'label' => 'Expiring Soon',
				'badge' => 'bg-warning text-dark',
				'text_color' => 'text-warning'
			];
		}

		// Active
		return [
			'code' => 'active',
			'label' => __('misc.active'),
			'badge' => 'bg-success',
			'text_color' => 'text-success'
		];
	}

	public function getFormattedAmountAttribute()
	{
		$amount = $this->amount ?: ($this->invoice ? $this->invoice->amount : ($this->interval === 'year' ? 27.00 : 3.00));
		$curr = $this->currency ?: ($this->payment_gateway === 'Razorpay' ? 'INR' : 'USD');
		return \App\Helper::formatPrice($amount, $curr);
	}

	public function getDaysRemainingAttribute()
	{
		if (!$this->ends_at) return null;
		$endsAt = \Carbon\Carbon::parse($this->ends_at);
		if ($endsAt->isPast()) {
			return 'Expired ' . $endsAt->diffForHumans();
		}
		return 'Expires ' . $endsAt->diffForHumans();
	}
}
