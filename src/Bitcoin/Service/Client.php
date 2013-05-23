<?php

namespace Bitcoin\Service;
use Zend\Http\Client as HttpClient;
use Zend\Http\Client\Adapter\Curl as CurlAdapter;
use Zend\Json;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;

class Client implements ServiceManagerAwareInterface
{
    protected $blockchain_identifier;
    protected $blockchain_password;
    protected $url;
    protected $client;
    protected $service_manager;
    
    public function __construct(array $bitcoin)
    {

        $this->blockchain_identifier = $bitcoin['blockchain_identifier'];
        $this->blockchain_password = $bitcoin['blockchain_password'];
        $this->url = 'https://blockchain.info/merchant/'.$this->blockchain_identifier;
        
        $this->client = new HttpClient();

        $adapter = new CurlAdapter();        
        $adapter->setCurlOption(CURLOPT_SSL_VERIFYPEER, false);

        $this->client->setAdapter($adapter);
        $this->client->setMethod('GET');
    }

    /**
     * Returns the balance for a given Bitcoin Address
     * @param $address
     * @return string
     */
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

    /**
     * Amounts in Satoshis
     * @param $from
     * @param $to
     * @param $amount
     * @param int $fee
     * @param null $note
     * @return string
     */
    public function sendBitcoin($from,$to,$amount,$fee = 50000, $note = null)
    {
        $this->client->setUri($this->url.'/payment');
        $this->client->setParameterGet(array(
            'password' => $this->blockchain_password,
            'from' => $from,
            'to' => $to,
            'amount' => $amount,
            'fee' => $fee,
            'note' => $note
        ));
        $json =  $this->process();
        return $json;
    }

    /**
     * Amounts in Satoshis
     * @param $from
     * @param $recipients array('address' => 'amount')
     * @param int $fee
     * @param null $note
     * @return string
     */
    public function sendMultipleTransactions($from,$recipients,$fee = 50000, $note = null)
    {
        $this->client->setUri($this->url.'/sendmany');
        $this->client->setParameterGet(array(
            'password' => $this->blockchain_password,
            'from' => $from,
            'recipients' => $recipients,
            'fee' => $fee,
            'note' => $note
        ));
        $json =  $this->process();
        return $json;
    }

    /**
     *
     * @return string
     */
    public function listWalletAddresses()
    {
        $this->client->setUri($this->url.'/list');
        $this->client->setParameterGet(array(
            'password' => $this->blockchain_password
        ));
        $json =  $this->process();
        return $json;
    }

    /**
     * @param null $label
     * @return string
     */
    public function generateNewAddress($label = null)
    {
        $this->client->setUri($this->url.'/new_address');
        $this->client->setParameterGet(array(
            'password' => $this->blockchain_password,
            'label' => $label
        ));
        $json =  $this->process();
        return $json;
    }

    /**
     * To improve wallet performance addresses which have not been used recently
     * should be moved to an archived state. They will still be held in the wallet
     * but will no longer be included in the "list" or "list-transactions" calls.
     * @param $address
     * @return string
     */
    public function archiveAddress($address)
    {
        $this->client->setUri($this->url.'/archive_address');
        $this->client->setParameterGet(array(
            'password' => $this->blockchain_password,
            'address' => $address
        ));
        $json =  $this->process();
        return $json;
    }

    /**
     * Unarchive an address. Will also restore consolidated addresses
     * @param $address
     * @return string
     */
    public function unarchiveAddress($address)
    {
        $this->client->setUri($this->url.'/unarchive_address');
        $this->client->setParameterGet(array(
            'password' => $this->blockchain_password,
            'address' => $address
        ));
        $json =  $this->process();
        return $json;
    }

    /**
     * Queries to wallets with over 10 thousand addresses will become sluggish especially in the web interface.
     * The auto_consolidate command will remove some inactive archived addresses from the wallet and insert them as
     * forwarding addresses (see receive payments API).
     * If generating a lot of addresses it is a recommended to call this method at least every 48 hours.
     * @param $days
     * @return string
     */
    public function autoConsolidateAddresses($days)
    {
        $this->client->setUri($this->url.'/auto_consolidate');
        $this->client->setParameterGet(array(
            'password' => $this->blockchain_password,
            'days' => $days
        ));
        $json =  $this->process();
        return $json;
    }
    
    protected function process()
    {
        $response = $this->client->send();
        $body = $response->getBody();
        return $body;
    }

    /**
     * Set service manager
     *
     * @param ServiceManager $serviceManager
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->service_manager = $serviceManager;
    }

    /**
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->service_manager;
    }


}