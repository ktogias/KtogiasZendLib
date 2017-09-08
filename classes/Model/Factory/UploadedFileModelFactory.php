<?php
/**
 * KtogiasZendLib (https://github.com/ktogias/KtogiasZendLib)
 *
 * @link      https://github.com/ktogias/KtogiasZendLib for the canonical source repository
 * @copyright Copyright (c) Konstantinos Togias (https://ktogias.gr)
 * @license   https://raw.githubusercontent.com/ktogias/KtogiasZendLib/master/LICENSE New BSD License
 */


namespace KtogiasZendLib\Model\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Description of UploadedFileModelFactory
 *
 * @author ktogias
 */
abstract class UploadedFileModelFactory implements FactoryInterface{
    
    /**
     *
     * @var string
     */
    protected $uploadDir;
    
    /**
     *
     * @var string
     */
    protected $inputFilterClass;
    
    /**
    * @var string[]
    */
    protected $mimeTypes;
    
    /**
     *
     * @var string[] 
     */
    protected $extensions;
    
    /**
     * @var string 
     */
    protected $maxSize;
    
    public function createService(ServiceLocatorInterface $serviceLocator) {
        $modelClass = $this->getModelClass();
        $model = new $modelClass();
        /* @var $model KtogiasZendLib\Model\UploadedFileModelInterface */
        $model->setUploadDir($this->uploadDir);
        $model->setMimeTypes($this->mimeTypes);
        $model->setExtensions($this->extensions);
        $model->setMaxSize($this->maxSize);
        $model->setServiceLocator($serviceLocator);
        $inputFilter = $this->getInputFilter($model);
        /*@var $inputFilter Zend\InputFilter\InputFilterInterface */
        $model->setInputFilter($inputFilter);
        return $model;
    }
    
    private function getModelClass(){
        $refClass = new \ReflectionObject($this);
        $stripFactory = substr($refClass->getName(), 0, strrpos($refClass->getName(), 'Factory'));
        return strtr($stripFactory, array('\Factory' => ''));
    }
    
    private function getInputFilter(\KtogiasZendLib\Model\UploadedFileModelInterface $model){
        $refClass = new \ReflectionClass($this->inputFilterClass);
        if ($refClass->implementsInterface('\KtogiasZendLib\InputFilter\UploadedFileModelAwareInputFilterInterface')){
            return $refClass->newInstance($model);
        }
        return $refClass->newInstance();
    }
}
