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

/**
 * Description of ValidatingDbTableModelFactory
 *
 * @author ktogias
 */
class ValidatingDbTableModelFactory extends DbTableModelFactory implements FactoryInterface{
    protected $inputFilterClass;
    
    public function createService(ServiceLocatorInterface $serviceLocator) {
        $model = parent::createService($serviceLocator);
        /* @var $model KtogiasZendLib\Model\ValidatingDbTableModelInterface */
        $inputFilter = $this->getInputFilter($model);
        /*@var $inputFilter Zend\InputFilter\InputFilterInterface */
        $model->setInputFilter($inputFilter);
        return $model;
    }
    
    private function getInputFilter(\KtogiasZendLib\Model\DbTableModelInterface $model){
        $refClass = new \ReflectionClass($this->inputFilterClass);
        if ($refClass->implementsInterface('\KtogiasZendLib\InputFilter\DbTableModelAwareInputFilterInterface')){
            return $refClass->newInstance($model);
        }
        return $refClass->newInstance();
    }
}
