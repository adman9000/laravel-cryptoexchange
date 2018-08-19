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
       
   
      public function __call($name, $arguments)
    {
        $this->function = $name;

        try {
            $result = $this->api->$name($arguments);

        } catch( \Exception $e) {
            $result = [];
            $result['fail'] = true;
            $result['errors'][] = $e->getMessage();
            return $result;
        }

        return $this->formatResult($result, "currencies");

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