# laravel-cryptoexchange
Crypto tradingg via multiple exchanges for Laravel

## Install

#### Install via Composer

```
composer require adman9000/laravel-cryptoexchange
```

## Requires

adman9000/laravel-kraken
adman9000/laravel-bittrex
adman9000/laravel-binance
adman9000/laravel-cryptopia

## Features

Price tickers, markets and currencies

Allows consistent interaction between multiple cryptoexchange APIs. 

## Usage

Pass the exchange name in as the first parameter on the constructor, API key and secret as 2nd & 3rd if needed.

```php
$exchange = new Exchange('kraken');
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

