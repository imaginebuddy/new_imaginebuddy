<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Plans;
use App\Models\Images;
use App\Models\Deposits;
use App\Models\TaxRates;
use App\Models\Downloads;
use App\Models\Languages;
use App\Models\Categories;
use App\Models\Withdrawals;
use App\Models\AdminSettings;
use App\Models\UsersReported;
use App\Models\ImagesReported;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		try {
			$settings = AdminSettings::first();
			$categoriesCount = Categories::count();
			$categoriesMain = Categories::where('mode', 'on')->orderBy('name')->take(5)->get();
			$languages = Languages::orderBy('name')->get();
			$taxRatesCount = TaxRates::whereStatus('1')->count();
			$userCount = User::whereStatus('active')->count();
			$downloadsCount = Downloads::count();
			$imagesCount = Images::whereStatus('active')->count();
			$plansActive = Plans::whereStatus('1')->count();
			$depositsPendingCount = Deposits::selectRaw('COUNT(id) as total')->whereStatus('pending')->pluck('total')->first();
			$withdrawalsPendingCount = Withdrawals::selectRaw('COUNT(id) as total')->whereStatus('pending')->pluck('total')->first();
			$imagesPendingCount = Images::selectRaw('COUNT(id) as total')->whereStatus('pending')->pluck('total')->first();
			$usersReported = UsersReported::selectRaw('COUNT(id) as total')->pluck('total')->first();
			$imagesReported = ImagesReported::selectRaw('COUNT(id) as total')->pluck('total')->first();

			view()->share(compact(
				'settings',
				'categoriesCount',
				'categoriesMain',
				'languages',
				'taxRatesCount',
				'userCount',
				'downloadsCount',
				'imagesCount',
				'plansActive',
				'depositsPendingCount',
				'withdrawalsPendingCount',
				'imagesPendingCount',
				'usersReported',
				'imagesReported'
			));
		} catch (\Exception $e) {
		}
	}

	/**
	 * Register any application services.
	 *
	 * This service provider is a great spot to register your various container
	 * bindings with the application. As you can see, we are registering our
	 * "Registrar" implementation here. You can add your own bindings too!
	 *
	 * @return void
	 */
	public function register()
	{
	}
}
