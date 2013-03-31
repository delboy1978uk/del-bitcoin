<?php 
namespace Bitcoin\Controller;
 
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Http\Client as HttpClient;
 
class ClientController extends AbstractActionController
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
        $this->client->setAdapter('Zend\Http\Client\Adapter\Curl');
        $this->client->setMethod('GET');
    }
    
    public function indexAction()
    {   
        $host = $this->getRequest()->getBaseUrl();
        $method = $this->params()->fromQuery('method', 'get');
        $address = $this->params('address');
        switch($method) {
            case 'get' :
                $this->client->setUri($this->url.'/address_balance');
                $this->client->setParameterGet(array(
                    'password' => $this->blockchain_password,
                    'address' => $address,
                    'confirmations' => '0'
                ));
                break;
            case 'get-list' :
                $this->client->setMethod('GET');
                $this->client->setParameterGET(array('id'=>1));
                break;
            case 'create' :
                $this->client->setMethod('POST');
                $this->client->setParameterPOST(array('name'=>'samsonasik'));
                break;
            case 'update' :
                $data = array('name'=>'ikhsan');
                $adapter = $this->client->getAdapter();
                 
                $adapter->connect($host, 80);
                $uri = $this->client->getUri().'?id=1';
                // send with PUT Method, with $data parameter
                $adapter->write('PUT', new \Zend\Uri\Uri($uri), 1.1, array(), http_build_query($data)); 
                 
                $responsecurl = $adapter->read();
                list($headers, $content) = explode("\r\n\r\n", $responsecurl, 2);
                $response = $this->getResponse();
                  
                $response->getHeaders()->addHeaderLine('content-type', 'text/html; charset=utf-8');
                $response->setContent($content);
                 
                return $response;
            case 'delete' :
                $adapter = $this->client->getAdapter();
                 
                $adapter->connect($host, 80);
                $uri = $this->client->getUri().'?id=1'; //send parameter id = 1
                // send with DELETE Method
                $adapter->write('DELETE', new \Zend\Uri\Uri($uri), 1.1, array());
                 
                $responsecurl = $adapter->read();
                list($headers, $content) = explode("\r\n\r\n", $responsecurl, 2);
                $response = $this->getResponse();
                  
                $response->getHeaders()->addHeaderLine('content-type', 'text/html; charset=utf-8');
                $response->setContent($content);
                 
                return $response;
        }
        $json =  $this->process();
        return $json;
    }
    
    protected function process()
    {
        //if get/get-list/create
        $response = $this->client->send();
        if (!$response->isSuccess()) {
            // report failure
            $message = $response->getStatusCode() . ': ' . $response->getReasonPhrase();
             
            $response = $this->getResponse();
            $response->setContent($message);
            return $response;
        }
        $body = $response->getBody();
         
        $response = $this->getResponse();
        $response->setContent($body);
         
        return $response;
    }
}