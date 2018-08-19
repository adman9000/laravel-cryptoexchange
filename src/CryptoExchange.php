<?php 
namespace adman9000\cryptoexchange;

class CryptoExchange
{
    protected $exchange;     // kraken/binance/bittrex/cryptopia
    protected $key;     // API key
    protected $secret;  // API secret
    protected $api; //the api in use

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
        curl_close($this->curl);
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

        $this->api->setAPI($this->api_key, $this->api_secret);

    }
	   
   

    /**
     ---------- PUBLIC FUNCTIONS ----------
    * getTicker
    * getTickers
    * getCurrencies
    * getMarkets
    *
    *
    *
    * 
     **/


     /**
     * Get ticker
     *
     * @param asset pair code
     * @return asset pair ticker info
     */
    public function getTicker($code)
    {
        return $this->api->getTickers($code);
    }

     /**
     * Get tickers
     *
     * @param array $pairs
     * @return array of ticker info by pair codes
     */
    public function getTickers(array $pairs)
    {
        return $this->api->getTickers($pairs);
    }


	/**
     * Get currencies listed on this exchange
     *
     * @return array of asset names and their info
     */
    public function getCurrencies()
    {
        return $this->api->getCurrencies();
    }

  
	 /**
     * getMarkets()
     * @return array of trading pairs available on this exchange
     **/
    public function getMarkets(array $pairs=null)
    {
        return $this->api->getMarkets($pairs, 'info');
    }

  

}