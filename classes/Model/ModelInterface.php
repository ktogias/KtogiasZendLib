<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */


namespace KtogiasZendLib\Model;


use Zend\ServiceManager\ServiceLocatorInterface;

/**
 *
 * @author ktogias
 */
interface ModelInterface {
    
    /**
     * 
     * @param ServiceLocatorInterface $sl
     */
    public function setServiceLocator(ServiceLocatorInterface $sl);
    
    
    /**
     * 
     * @return Zend\ServiceManager\ServiceLocatorInterface
     */
    public function getServiceLocator();
    
}
