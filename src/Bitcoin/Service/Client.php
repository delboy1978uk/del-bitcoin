<?php

namespace Bitcoin\Service;
use Zend\Http\Client as HttpClient;
use Zend\Http\Client\Adapter\Curl as CurlAdapter;
use Zend\Json;

class Client
{
    protected $blockchain_identifier;
    protected $blockchain_password;
    protected $url;
    protected $client;
    
    public function __construct()
    {
        $this->blockchain_identifier = '6a3ae2a7-dabc-f7a4-f348-13635f28538b';
        $this->blockchain_password = 'j4m3580nd!';
        $this->url = 'https://blockchain.info/merchant/'.$this->blockchain_identifier;
        
        $this->client = new HttpClient();

        $adapter = new CurlAdapter();        
        $adapter->setCurlOption(CURLOPT_SSL_VERIFYPEER, false);

        $this->client->setAdapter($adapter);
        $this->client->setMethod('GET');
    }
    
    public function getWalletBalance($address)
    {
        $this->client->setUri($this->url.'/address_balance');
        $this->client->setParameterGet(array(
            'password' => $this->blockchain_password,
            'address' => $address,
            'confirmations' => '0'
        ));
        $json =  $this->process();

        $array = Json\Json::decode($json);
        return number_format(($array->balance/100000000),10);
    }
    
    protected function process()
    {
        $response = $this->client->send();
        $body = $response->getBody();
        return $body;
    }
}