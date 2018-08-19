# laravel-cryptoexchange
Crypto trading via multiple exchanges for Laravel

## Install

#### Install via Composer

```
composer require adman9000/laravel-cryptoexchange
```

## Requires

https://github.com/adman9000/laravel-binance
https://github.com/adman9000/laravel-cryptopia
https://github.com/adman9000/laravel-kraken
https://github.com/adman9000/laravel-bittrex

## Features

Price tickers, markets and currencies

Allows consistent interaction between multiple cryptoexchange APIs. Currently works with Binance, Kraken, Bittrex, Cryptopia.

## Usage

Pass the exchange name in as the first parameter on the constructor, API key and secret as 2nd & 3rd if needed.

```php
$exchange = new Exchange('kraken', $key, $secret);
$data = $exchange->getCurrencies();
```

Returned data will be consistently in the following format:

```php
[
'success' => boolean,
'fail' => boolean,
'errors' => array(),
'data' => array()
]
```

