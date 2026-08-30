<?php

namespace App\Http\Controllers;

use App\Helper;
use App\Models\Images;
use Illuminate\Http\Request;
use App\Models\PaymentGateways;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends Controller
{
	use Traits\FunctionsTrait;

	public function __construct(Request $request)
	{
		$this->request = $request;
	}

	public function send()
	{
		if (config('settings.sell_option') == 'off') {
			return response()->json([
				'success' => false,
				'errors' => ['error' => __('misc.purchase_not_allowed')],
			]);
		}

		Images::where('token_id', $this->request->token)
			->where('user_id', '<>', auth()->id())
			->firstOrFail();

		$messages = [
			'type.in' => __('misc.error'),
			'license.in' => __('misc.error')
		];

		//<---- Validation
		$validator = Validator::make($this->request->all(), [
			'type' => 'required|in:small,medium,large,vector',
			'license' => 'required|in:regular,extended',
			'payment_gateway' => 'required',
		], $messages);

		if ($validator->fails()) {
			return response()->json([
				'success' => false,
				'errors' => $validator->getMessageBag()->toArray(),
			]);
		}

		// Wallet
		if ($this->request->payment_gateway == 'wallet') {
			return $this->sendWallet();
		}

		// Get name of Payment Gateway
		$payment = PaymentGateways::find($this->request->payment_gateway);

		if (!$payment) {
			return response()->json([
				'success' => false,
				'errors' => ['error' => __('misc.payments_error')],
			]);
		}

		$routePayment = str_slug($payment->name) . '.buy';

		// Send data to the payment processor
		return redirect()->route($routePayment, $this->request->except(['_token']));
	}

	private function sendWallet()
	{
		// Get Image
		$image = Images::where('token_id', $this->request->token)->firstOrFail();

		$priceItem = config('settings.default_price_photos') ?: $image->price;

		$itemPrice = $this->priceItem($this->request->license, $priceItem, $this->request->type);

		if (auth()->user()->funds < Helper::amountGross($itemPrice)) {
			return response()->json([
				"success" => false,
				"errors" => ['error' => __('misc.not_enough_funds')]
			]);
		}

		// Admin and user earnings calculation
		$earnings = $this->earningsAdminUser($image->user()->author_exclusive, $itemPrice, null, null);

		// Insert purchase
		$this->purchase(
			'pw_' . str_random(25),
			$image,
			auth()->id(),
			$itemPrice,
			$earnings['user'],
			$earnings['admin'],
			$this->request->type,
			$this->request->license,
			$earnings['percentageApplied'],
			'Wallet',
			auth()->user()->taxesPayable()
		);

		// Subtract user funds
		auth()->user()->decrement('funds', Helper::amountGross($itemPrice));

		return response()->json([
			"success" => true,
			'url' => url('user/dashboard/purchases')
		]);
	}
}
