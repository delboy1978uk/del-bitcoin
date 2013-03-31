<?php 
    return array(
        'controllers' => array(
            'invokables' => array(
                'Bitcoin\Controller\Wallet' => 'Bitcoin\Controller\WalletController',
                'Bitcoin\Controller\Client' => 'Bitcoin\Controller\ClientController',
            ),
        ),
    
        
         
         'router' => array(
            'routes' => array(
                'Bitcoin' => array(
                    'type'    => 'Literal',
                    'options' => array(
                        // Change this to something specific to your module
                        'route'    => '/wallet',
                        'defaults' => array(
                            // Change this value to reflect the namespace in which
                            // the controllers for your module are found
                            'controller'    => 'Bitcoin\Controller\Wallet',
                        ),
                    ),
                     
                    'may_terminate' => true,
                    'child_routes' => array(
                        'client' => array(
                            'type'    => 'Segment',
                            'options' => array(
                                'route'    => '/client[/:method][/:address]',
                                'constraints' => array(
                                    'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                ),
                                'defaults' => array(
                                	'controller' => 'Bitcoin\Controller\Client',
                                    'action'     => 'index'
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    );