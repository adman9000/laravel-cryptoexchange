<?php namespace adman9000\cryptoexchange;

/**
 * @author  adman9000
 */
use Illuminate\Support\ServiceProvider;

class CryptoexchangeServiceProvider extends ServiceProvider {

	public function boot() 
	{
		$this->publishes([
			__DIR__.'/config/cryptoexchange.php' => config_path('cryptoexchange.php')
		]);
	} // boot

	public function register() 
	{
		$this->mergeConfigFrom(__DIR__.'/config/cryptoexchange.php', 'cryptoexchange');
		$this->app->bind('cryptoexchange', function() {
			return new Cryptoexchange(config('cryptoexchange'));
		});

		

	} // register
}