<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */



namespace KtogiasZendLib\Model;

/**
 * Description of Model
 *
 * @author ktogias
 */
abstract class Model implements ModelInterface{
    
    /**
     *
     * @var \Zend\ServiceManager\ServiceLocatorInterface
     */
    protected $serviceLocator;
    
    /**
     * 
     * @param \Zend\ServiceManager\ServiceLocatorInterface $sl
     */
    public function setServiceLocator(\Zend\ServiceManager\ServiceLocatorInterface $sl) {
        $this->serviceLocator = $sl;
    }
    
    /**
     * 
     * @return \Zend\ServiceManager\ServiceLocatorInterface
     */
    public function getServiceLocator() {
        return $this->serviceLocator;
    }
    
}
