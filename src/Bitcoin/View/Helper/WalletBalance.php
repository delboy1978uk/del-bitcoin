<?php

namespace Bitcoin\View\Helper;
use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;

class WalletBalance  extends AbstractHelper implements ServiceManagerAwareInterface
{
    
    protected $serviceManager;
    
	/**
     * __invoke
     *
     * @param string $address 
     * @access public
     * @return string
     */
    public function __invoke($address = null)
    {
        if(isset($address))
        {
	    	$service = $this->getServiceManager()->getServiceLocator()->get('bitcoin_client_service');
	        $balance = $service->getWalletBalance($address);
			return $balance;
        }
    }
    
	public function getServiceManager()
    {
        return $this->serviceManager;
    }

    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
        return $this;
    }
}