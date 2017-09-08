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
 * Description of AuthAwareValidatingDbTableModelFactory
 *
 * @author ktogias
 */
class AuthAwareValidatingDbTableModelFactory extends ValidatingDbTableModelFactory implements FactoryInterface{
    
    public function createService(ServiceLocatorInterface $serviceLocator) {
        $model = parent::createService($serviceLocator);
        /* @var $model KtogiasZendLib\Model\AuthAwareValidatingDbTableModelInterface */
        $model->setAuth(new UserAuthenticationService($serviceLocator));
        return $model;
    }

}
