<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */

namespace KtogiasAutoAuth;

use Zend\Mvc\MvcEvent;
use Zend\Authentication\AuthenticationService;

class Module extends \KtogiasZendLib\Module\Module {
    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;
    
    public function onBootstrap(MvcEvent $e) {
        $config = $e->getApplication()->getServiceManager()->get('config');
        $eventManager = $e->getApplication()->getEventManager();
         if (isset($config['authentication']['auto-auth']) && $config['authentication']['auto-auth']){
            $eventManager->attach('route', function(MvcEvent $e){
                $this->autoAuthenticate($e);
            }, -200);
        }
    }
    
    protected function autoAuthenticate(MvcEvent $e){
        $config = $e->getApplication()->getServiceManager()->get('config');
        if (empty($config['authentication']['auth-adapter'])){
            throw new Exception\AuthAdapterNotSetException;
        }
        $identityFactory = null;
        if (!empty($config['authentication']['identity-factory'])){
            $identityFactory = new $config['authentication']['identity-factory']();
            if ($identityFactory instanceof \Zend\ServiceManager\FactoryInterface){
                /*@var $identityFactory \Zend\ServiceManager\FactoryInterface*/
                $identityFactory->createService($e->getApplication()->getServiceManager());
            }
        }
        $adapter = new $config['authentication']['auth-adapter']($config['authentication']['auth-adapter-options'], null, $identityFactory);
        $auth = new AuthenticationService();
        if(!$auth->hasIdentity()){
            $auth->authenticate($adapter);
        }
    }
}