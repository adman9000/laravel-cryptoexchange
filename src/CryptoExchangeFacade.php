<?php namespace adman9000\cryptoexchange;

use Illuminate\Support\Facades\Facade;

class CryptoExchangeFacade extends Facade {

	protected static function getFacadeAccessor() {
		return 'cryptoexchange';
	}
}