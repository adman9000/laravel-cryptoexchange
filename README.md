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

Retrieve data from multiple cryptocurrency exchanges. Retrieves and formats currency lists, markets, tickers and account balances.

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

## Currently Supported Functions

```php
getCurrencies();
//returned data array consists of ['code', 'title', 'decimals'];

getMarkets();
//returned associative data array consists of ['code', 'base', 'alt'];

getTicker($code);
//returned data array consists of ['code', 'price'];

getTickers();
//returned associative data array consists of ['code', 'price'];

getBalances();
//returned associative data array consists of ['code', 'available', 'locked', 'balance'];

depositAddress();
//returned data array consists of ['code', 'address'];

```

## To Be Added

```php
marketBuy()

marketSell()

limitBuy()

limitSell()

withdrawFunds()

withdrawalHistory()

depositHistory()

```
