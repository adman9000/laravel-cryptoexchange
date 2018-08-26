<?php 
namespace adman9000\cryptoexchange;

use adman9000\cryptopia\CryptopiaAPI;
use adman9000\kraken\KrakenAPI;
use adman9000\binance\BinanceAPI;
use adman9000\bittrex\BittrexAPI;

class CryptoExchange
{
    protected $exchange;     // kraken/binance/bittrex/cryptopia
    protected $key;     // API key
    protected $secret;  // API secret
    protected $api; //the api in use
    protected $function;

    protected $btc_usd_codes;
    /**
     * Constructor for CryptoExchange
     *
     * @param string $exchange Exchange 'slug'
     * @param string $key API key
     * @param string $secret API secret 
     */
    function __construct($exchange, $key=false, $secret=false)
    {
        $this->setExchange($exchange, $key, $secret);

        $this->btc_usd_codes = [
            'kraken' => 'XBTUSD',
            'binance' => 'BTCUSDT',
            'bittrex' => 'USDT-BTC',
            'cryptopia' => 'BTC/USDT'
        ];

    }

    function __destruct()
    {
        
    }

     /**
     * setExchange()
     *
     * @param string $exchange Exchange 'slug'
     * @param string $key API key
     * @param string $secret API secret 
     */
    public function setExchange($exchange, $key, $secret) {

        $this->exchange = $exchange;
        $this->key = $key;
        $this->secret = $secret;

        $this->selectExchangeAPI();
    }

    /**
     * selectExchangeAPI()
     *
     **/
    protected function selectExchangeAPI() {

        switch($this->exchange) {
            case "kraken" :
                $this->api = new KrakenAPI();
                break;
            case "binance" :
                $this->api = new BinanceAPI();
                break;
            case "bittrex" :
                $this->api = new BittrexAPI();
                break;
            case "cryptopia" :
                $this->api = new CryptopiaAPI();
                break;
        }

        $this->api->setAPI($this->key, $this->secret);

    }
	   
    function setAPIKey($key, $secret) {

        $this->api->setAPI($key, $secret);
    }
   

      public function __call($name, $arguments)
    {
        $this->function = $name;

        try {
           
           //Work this into a separate function
            if(($this->exchange == "kraken") && ($this->function == "getTickers")) {

                //Kraken is crap, needs the list of markets for the ticker so got to do 2 api calls (unless the argument is set already)
                if(!isset($arguments[0])) {
                    
                    $temp = $this->getMarkets();
                    
                    foreach($temp['data'] as $market) {
                        $markets[] = $market['code'];
                    }
                    $arguments[0] = $markets;
                }
                $this->function = "getTickers"; //needs to be reset to do correct formatting
            }

            $result = call_user_func_array([$this->api, $name],$arguments);

        } catch( \Exception $e) {
            $result = [];
            $result['fail'] = true;
            $result['errors'][] = $e->getMessage();
            return $result;
        }

        return $this->formatResult($result);

    }


    /** Custom functions **/

    /** getBTCUSDTicker()
     **/
    function getBTCUSDTicker() {

        //Different exchanges have different codes
        $code = $this->btc_usd_codes[$this->exchange];

        return $this->getTicker($code);

    }


    /**
     * formatResult();
     * @param $result
     * @param result type
     * Consistent format for results across all APIs
     */
    function formatResult($result) {

        $response = [];


        switch($this->exchange) {

            case "kraken" :

                if(sizeof($result['error'])==0) {
                    $response['success'] = true;
                    $response['data'] = [];

                    switch($this->function) {

                    case "getCurrencies" :

                        foreach($result['result'] as $code=>$data) {

                            $asset['code'] = $code;
                            $asset['title'] = $data['altname'];
                            $asset['decimals'] = $data['decimals'];
                            $response['data'][] = $asset;
                        }

                    break;

                    case "getMarkets" :

                        foreach($result['result'] as $code=>$data) {

                            $asset['code'] = $code;
                            $asset['base'] = $data['base'];
                            $asset['alt'] = $data['quote'];
                            $response['data'][$asset['code']] = $asset;
                        }

                    break;

                    case "getTicker" :
                    case "getTickers" :
                            foreach($result['result'] as $code=>$data) {

                                $asset['code'] = $code;
                                $asset['price'] = $data['c'][0];
                                $response['data'][$asset['code']] = $asset;
                            }

                        break;

                    case "getBalances" :

                        foreach($result['result'] as $code=>$balance) {
                            $asset['code'] = $code;
                            $asset['balance'] = $balance;
                            $asset['available'] = $balance;
                            $asset['locked'] = 0;
                            $response['data'][$asset['code']] = $asset;
                        }

                        break;


                    case "depositAddress" :
                            $asset['code'] = '';
                            $asset['address'] = $result['result'][0]['address'];
                            $response['data'] = $asset;
                        break;

                    }

                }
                else {
                    $response['fail'] = true;
                    $response['errors'] = $result['error'];
                }

                

            break;

            case "binance" :

                if(sizeof($result)>0) {
                    $response['success'] = true;
                    $response['data'] = [];

                    switch($this->function) {

                    case "getCurrencies" :

                            foreach($result as $data) {

                                $asset['code'] = $data['code'];
                                $asset['title'] = $data['name'];
                                $asset['decimals'] = false;
                                $response['data'][] = $asset;
                            }

                        break;

                    case "getMarkets" :

                        foreach($result as $data) {

                            $asset['code'] = $data['symbol'];
                            $asset['base'] = $data['baseAsset'];
                            $asset['alt'] = $data['quoteAsset'];
                            $response['data'][$asset['code']] = $asset;
                        }

                    break;

                    case "getTicker" :
                    case "getTickers" :

                            foreach($result as $data) {

                                $asset['code'] = $data['symbol'];
                                $asset['price'] = $data['price'];
                                $response['data'][$asset['code']] = $asset;
                            }

                        break;

                    case "getBalances" :

                        foreach($result as $data) {
                            $asset['code'] = $data['asset'];
                            $asset['available'] = $data['free'];
                            $asset['locked'] = $data['locked'];
                            $asset['balance'] = $asset['locked'] + $asset['available'];
                            $response['data'][$asset['code']] = $asset;
                        }

                        break;

                    case "depositAddress" :
                            $asset['code'] = $result['asset'];
                            $asset['address'] = $result['address'];
                            $response['data'] = $asset;
                        break;

                    }


                }
                else {
                    $response['fail'] = true;
                    $response['errors'] = $result['error'];
                }

                

            break;

            case "bittrex" :

                if($result['success']) {
                    $response['success'] = true;
                    $response['data'] = [];

                    switch($this->function) {

                    case "getCurrencies" :

                            foreach($result['result'] as $data) {

                                $asset['code'] = $data['Currency'];
                                $asset['title'] = $data['CurrencyLong'];
                                $asset['decimals'] = false;
                                $response['data'][] = $asset;
                            }

                        break;

                    case "getMarkets" :

                        foreach($result['result'] as $data) {

                            $asset['code'] = $data['MarketName'];
                            $asset['base'] = $data['BaseCurrency'];
                            $asset['alt'] = $data['MarketCurrency'];
                            $response['data'][$asset['code']] = $asset;
                        }

                    break;

                    case "getTicker" :
                    case "getTickers" :
                            foreach($result['result'] as $data) {

                                $asset['code'] = $data['MarketName'];
                                $asset['price'] = $data['Last'];
                                $response['data'][$asset['code']] = $asset;
                            }

                        break;

                    case "getBalances" :

                        foreach($result['result'] as $data) {
                            $asset['code'] = $data['Currency'];
                            $asset['available'] = $data['Pending'];
                            $asset['locked'] = $data['Available'];
                            $asset['balance'] = $data['Balance'];
                            $response['data'][$asset['code']] = $asset;
                        }

                        break;

                    case "depositAddress" :
                            $asset['code'] = $result['result']['Currency'];
                            $asset['address'] = $result['result']['Address'];
                            $response['data'] = $asset;
                        break;
                    }


                }
                else {
                    $response['fail'] = true;
                    $response['errors'][] = $result['message'];
                }

                
            break;

            case "cryptopia" :

                if(sizeof($result)>0) {
                    $response['success'] = true;
                    $response['data'] = [];

                    switch($this->function) {

                    case "getCurrencies" :

                            foreach($result as $data) {

                                $asset['code'] = $data['Symbol'];
                                $asset['title'] = $data['Name'];
                                $asset['decimals'] = false;
                                $response['data'][] = $asset;
                            }

                        break;

                    case "getMarkets" :

                        foreach($result as $data) {

                            $asset['code'] = $data['Label'];
                            $asset['base'] = $data['BaseSymbol'];
                            $asset['alt'] = $data['Symbol'];
                            $response['data'][$asset['code']] = $asset;
                        }

                    break;

                    case "getTicker" :
                    case "getTickers" :

                            foreach($result as $data) {

                                $asset['code'] = $data['Label'];
                                $asset['price'] = $data['LastPrice'];
                                $response['data'][$asset['code']] = $asset;
                            }

                        break;

                    case "getBalances" :

                        foreach($result as $data) {
                            $asset['code'] = $data['Symbol'];
                            $asset['available'] = $data['Available'];
                            $asset['locked'] = $data['Unconfirmed'] + $data['HeldForTrades'] + $data['PendingWithdraw'];
                            $asset['balance'] = $data['Total'];
                            $response['data'][$asset['code']] = $asset;
                        }

                        break;

                    case "depositAddress" :
                            $asset['code'] = $result['Data']['Currency'];
                            $asset['address'] = $result['Data']['Address'];
                            $response['data'] = $asset;
                        break;

                    }

                }
                else {
                    $response['fail'] = true;
                    $response['errors'][] = "error";
                }

                

            break;

        }

        return $response;
    }

  

}