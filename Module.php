<?php 

namespace Bitcoin;

class Module
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
				'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__
                 )
             )
        );
    }
    
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
    
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'bitcoin_client_service' => function ($sm) {
                    return new \Bitcoin\Service\Client(
                        $sm->get('config')['bitcoin']
                    );
                }
            ),
        );
    }
    
    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'wallet_balance' => function ($sm) {
                    $viewHelper = new View\Helper\WalletBalance;
                    $viewHelper->setServiceManager($sm);
                    return $viewHelper;
                }
            ),
        );

    }


}