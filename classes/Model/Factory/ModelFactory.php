<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */


/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace KtogiasZendLib\Model\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use KtogiasZendLib\Authentication\UserAuthenticationService;

/**
 * Description of ReadOnlyDbTableModelFactory
 *
 * @author ktogias
 */
class ModelFactory implements FactoryInterface{
    
    public function createService(ServiceLocatorInterface $serviceLocator) {
        $modelClass = $this->getModelClass();
        $model = new $modelClass();
        
        if(is_subclass_of($model, 'KtogiasZendLib\Logging\LoggingAwareInterface')){
            try{
                $logModel = $serviceLocator->get('KtogiasZendLib\Application\Log\Model\LogModel');
                $model->setLogModel($logModel);
            }
            catch(\Zend\ServiceManager\Exception\CircularDependencyFoundException $e){
            }
        }
        if (is_subclass_of($model, 'KtogiasZendLib\Authentication\UserAuthenticationServiceAwareInterface')){
            try {
                $auth = new UserAuthenticationService($serviceLocator);
                $model->setUserAuthenticationService($auth);
            }
            catch(\Zend\ServiceManager\Exception\CircularDependencyFoundException $e){
            }
        }
        $model->setServiceLocator($serviceLocator);
        return $model;
    }
    
    private function getModelClass(){
        $refClass = new \ReflectionObject($this);
        $stripFactory = substr($refClass->getName(), 0, strrpos($refClass->getName(), 'Factory'));
        return strtr($stripFactory, array('\Factory' => ''));
    }
    
}
